<?php
require 'db.php';
require 'auth.php';
require_login();
header('Content-Type: application/json; charset=utf-8');
$data = json_decode(file_get_contents('php://input'), true) ?: [];
$scenarioId = (int)($data['scenario_id'] ?? 0);
$fix = (string)($data['fix_type'] ?? '');
$timeTaken = max(0, min(299, (int)($data['time_taken'] ?? 0)));
$userId = (int)$_SESSION['user_id'];

$stmt = $pdo->prepare('SELECT correct_fix,fix_message,fail_message,xp_reward FROM scenarios WHERE scenario_id=?');
$stmt->execute([$scenarioId]);
$s = $stmt->fetch();
if (!$s) { http_response_code(404); echo json_encode(['ok'=>false,'message'=>'Scenario not found']); exit; }
$isCorrect = hash_equals($s['correct_fix'], $fix);

$pdo->beginTransaction();
try {
    $pdo->prepare('INSERT INTO attempts(user_id,scenario_id,selected_fix,is_correct,time_taken_seconds) VALUES(?,?,?,?,?)')
        ->execute([$userId,$scenarioId,$fix,$isCorrect?1:0,$timeTaken]);
    $xpAdded = 0; $newBadge = false;
    if ($isCorrect) {
        $p = $pdo->prepare('SELECT completed_at,best_time_seconds FROM user_scenario_progress WHERE user_id=? AND scenario_id=? FOR UPDATE');
        $p->execute([$userId,$scenarioId]);
        $progress = $p->fetch();
        if (!$progress) {
            $pdo->prepare('INSERT INTO user_scenario_progress(user_id,scenario_id,best_time_seconds,completed_at) VALUES(?,?,?,NOW())')->execute([$userId,$scenarioId,$timeTaken]);
            $xpAdded = (int)$s['xp_reward'];
            $pdo->prepare('UPDATE users SET total_xp=total_xp+? WHERE user_id=?')->execute([$xpAdded,$userId]);
        } else {
            $best = $progress['best_time_seconds'];
            if ($best === null || $timeTaken < (int)$best) {
                $pdo->prepare('UPDATE user_scenario_progress SET best_time_seconds=? WHERE user_id=? AND scenario_id=?')->execute([$timeTaken,$userId,$scenarioId]);
            }
        }
        $a = $pdo->prepare('SELECT achievement_id FROM achievements WHERE scenario_id=?');
        $a->execute([$scenarioId]);
        $achievementId = (int)$a->fetchColumn();
        if ($achievementId) {
            $ins = $pdo->prepare('INSERT IGNORE INTO user_achievements(user_id,achievement_id) VALUES(?,?)');
            $ins->execute([$userId,$achievementId]);
            $newBadge = $ins->rowCount() > 0;
        }
    }
    $xpQ = $pdo->prepare('SELECT total_xp FROM users WHERE user_id=?'); $xpQ->execute([$userId]);
    $totalXp = (int)$xpQ->fetchColumn();
    $pdo->commit();
    echo json_encode(['ok'=>true,'correct'=>$isCorrect,'message'=>$isCorrect?$s['fix_message']:$s['fail_message'],'xp_added'=>$xpAdded,'total_xp'=>$totalXp,'new_badge'=>$newBadge]);
} catch (Throwable $e) {
    $pdo->rollBack(); http_response_code(500); echo json_encode(['ok'=>false,'message'=>'Could not save attempt.']);
}
