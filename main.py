import customtkinter as ctk
from tkinter import messagebox

ctk.set_appearance_mode('dark')
ctk.set_default_color_theme('blue')


class SmartFixApp(ctk.CTk):
    def __init__(self):
        super().__init__()

        self.title('SMARTFIX-AI Simulator')
        self.geometry('1200x750')

        # Student profile
        self.student_name = 'Ali'
        self.level = 4
        self.xp = 2350
        self.max_xp = 3500

        # Case data
        self.case = {
            'id': 21,
            'title': 'Hardware Issue',
            'complaint': 'My computer turns on, but there is no display on the monitor.',
            'correct_action': 'Reseat RAM'
        }

        self.create_header()
        self.create_sidebar()
        self.create_main_panel()
        self.create_diagnostic_panel()

    def create_header(self):
        header = ctk.CTkFrame(self, height=70, corner_radius=0)
        header.pack(fill='x')

        title = ctk.CTkLabel(
            header,
            text='SMARTFIX-AI | Interactive Computer Troubleshooting Simulator',
            font=('Arial', 24, 'bold')
        )
        title.pack(side='left', padx=20, pady=15)

        profile = ctk.CTkLabel(
            header,
            text=f'Welcome, {self.student_name} | Level {self.level} Technician | {self.xp}/{self.max_xp} XP',
            font=('Arial', 14)
        )
        profile.pack(side='right', padx=20)

    def create_sidebar(self):
        sidebar = ctk.CTkFrame(self, width=220)
        sidebar.pack(side='left', fill='y', padx=10, pady=10)

        ctk.CTkLabel(sidebar, text='CHOOSE A CASE', font=('Arial', 16, 'bold')).pack(pady=10)

        cases = [
            'Hardware Issues',
            'Boot & BIOS Problems',
            'Software Installation',
            'Driver / Device Issues',
            'Performance Issues',
            'Network Issues',
            'Mixed Challenges'
        ]

        for case in cases:
            ctk.CTkButton(sidebar, text=case, width=180).pack(pady=4)

        ctk.CTkLabel(sidebar, text='MY PROGRESS', font=('Arial', 16, 'bold')).pack(pady=20)

        self.progress_bar = ctk.CTkProgressBar(sidebar, width=180)
        self.progress_bar.pack(pady=10)
        self.progress_bar.set(0.78)

        ctk.CTkLabel(sidebar, text='Overall Progress: 78%').pack()

        stats = ctk.CTkFrame(sidebar)
        stats.pack(pady=15, fill='x', padx=10)

        ctk.CTkLabel(stats, text='Cases Solved: 36').pack(anchor='w', padx=10, pady=2)
        ctk.CTkLabel(stats, text='Average Score: 89%').pack(anchor='w', padx=10, pady=2)
        ctk.CTkLabel(stats, text='Badges: 12').pack(anchor='w', padx=10, pady=2)

    def create_main_panel(self):
        self.main = ctk.CTkFrame(self)
        self.main.pack(side='left', fill='both', expand=True, padx=10, pady=10)

        top = ctk.CTkFrame(self.main)
        top.pack(fill='x', pady=5)

        ctk.CTkLabel(
            top,
            text=f'CASE #{self.case["id"]}',
            font=('Arial', 26, 'bold')
        ).pack(anchor='w', padx=10, pady=(10, 0))

        ctk.CTkLabel(
            top,
            text=self.case['title'],
            font=('Arial', 16)
        ).pack(anchor='w', padx=10)

        ctk.CTkLabel(
            top,
            text='Customer Complaint:',
            font=('Arial', 14, 'bold')
        ).pack(anchor='w', padx=10, pady=(10, 0))

        ctk.CTkLabel(
            top,
            text=f'"{self.case["complaint"]}"',
            font=('Arial', 15),
            wraplength=700,
            justify='left'
        ).pack(anchor='w', padx=20, pady=(0, 10))

        timer_frame = ctk.CTkFrame(top)
        timer_frame.pack(anchor='e', padx=10, pady=5)

        ctk.CTkLabel(timer_frame, text='TIME REMAINING', font=('Arial', 12)).pack()
        ctk.CTkLabel(timer_frame, text='04:59', font=('Arial', 22, 'bold')).pack()

        center = ctk.CTkFrame(self.main)
        center.pack(fill='both', expand=True, pady=10)

        ctk.CTkLabel(
            center,
            text='INVESTIGATE THE SYSTEM',
            font=('Arial', 18, 'bold')
        ).pack(anchor='w', padx=10, pady=10)

        pc_frame = ctk.CTkFrame(center, width=520, height=340)
        pc_frame.pack(pady=10)
        pc_frame.pack_propagate(False)

        ctk.CTkLabel(
            pc_frame,
            text='DESKTOP PC SIMULATION',
            font=('Arial', 20, 'bold')
        ).pack(pady=10)

        components = [
            ('CPU Fan', 'green'),
            ('GPU', 'green'),
            ('RAM', 'red'),
            ('Storage', 'green'),
            ('Power Supply', 'green'),
            ('Monitor Cable', 'yellow')
        ]

        grid = ctk.CTkFrame(pc_frame)
        grid.pack(expand=True)

        for i, (name, color) in enumerate(components):
            frame = ctk.CTkFrame(grid, width=140, height=70)
            frame.grid(row=i // 2, column=i % 2, padx=10, pady=10)
            frame.grid_propagate(False)

            symbol = {'green': '✓', 'red': '!', 'yellow': '?'}[color]

            ctk.CTkLabel(frame, text=symbol, font=('Arial', 24, 'bold')).pack(pady=(8, 0))
            ctk.CTkLabel(frame, text=name, font=('Arial', 12)).pack()

        steps_frame = ctk.CTkFrame(self.main)
        steps_frame.pack(fill='x', pady=10)

        ctk.CTkLabel(
            steps_frame,
            text='INVESTIGATION STEPS',
            font=('Arial', 16, 'bold')
        ).pack(anchor='w', padx=10, pady=5)

        steps = [
            ('✓', 'Check monitor power'),
            ('✓', 'Check monitor cable'),
            ('→', 'Inspect RAM'),
            ('', 'Test graphics output'),
            ('', 'Final diagnosis')
        ]

        for status, text in steps:
            ctk.CTkLabel(steps_frame, text=f'{status}  {text}', font=('Arial', 13)).pack(anchor='w', padx=20, pady=2)

        info = ctk.CTkFrame(self.main)
        info.pack(fill='x', pady=10)

        left = ctk.CTkFrame(info)
        left.pack(side='left', fill='both', expand=True, padx=5, pady=5)

        right = ctk.CTkFrame(info)
        right.pack(side='left', fill='both', expand=True, padx=5, pady=5)

        ctk.CTkLabel(left, text='CASE INFORMATION', font=('Arial', 16, 'bold')).pack(anchor='w', padx=10, pady=5)

        info_items = [
            ('Case ID', '21'),
            ('Reported by', 'Student'),
            ('Environment', 'Workshop Lab'),
            ('System Type', 'Desktop'),
            ('OS', '-')
        ]

        for k, v in info_items:
            ctk.CTkLabel(left, text=f'{k}: {v}', font=('Arial', 13)).pack(anchor='w', padx=15, pady=2)

        ctk.CTkLabel(right, text='NOTES', font=('Arial', 16, 'bold')).pack(anchor='w', padx=10, pady=5)

        note_text = (
            'No display detected. System powers on. '
            'Fans and lights are working.'
        )

        ctk.CTkLabel(right, text=note_text, wraplength=320, justify='left').pack(anchor='w', padx=15, pady=10)

    def create_diagnostic_panel(self):
        panel = ctk.CTkFrame(self, width=280)
        panel.pack(side='right', fill='y', padx=10, pady=10)

        ctk.CTkLabel(panel, text='DIAGNOSTIC PANEL', font=('Arial', 18, 'bold')).pack(pady=10)

        diagnostics = [
            ('CPU', 'OK'),
            ('RAM', 'NOT DETECTED'),
            ('GPU', 'OK'),
            ('Motherboard', 'UNKNOWN'),
            ('Storage', 'OK'),
            ('Power Supply', 'OK'),
            ('Monitor', 'NO SIGNAL')
        ]

        for item, status in diagnostics:
            row = ctk.CTkFrame(panel)
            row.pack(fill='x', padx=10, pady=3)

            ctk.CTkLabel(row, text=item, width=120, anchor='w').pack(side='left')

            color = '#4CAF50'
            if status in ['NOT DETECTED', 'NO SIGNAL']:
                color = '#F44336'
            elif status == 'UNKNOWN':
                color = '#FFC107'

            ctk.CTkLabel(row, text=status, text_color=color, anchor='e').pack(side='right')

        cause_frame = ctk.CTkFrame(panel)
        cause_frame.pack(fill='x', padx=10, pady=15)

        ctk.CTkLabel(cause_frame, text='POSSIBLE CAUSE', font=('Arial', 14, 'bold')).pack(anchor='w', padx=10, pady=5)

        cause_text = (
            '• RAM may not be properly seated or faulty.\\n'
            '• Check RAM installation.'
        )

        ctk.CTkLabel(cause_frame, text=cause_text, justify='left').pack(anchor='w', padx=10, pady=(0, 10))

        ctk.CTkLabel(panel, text='SELECT YOUR NEXT ACTION', font=('Arial', 16, 'bold')).pack(pady=10)

        actions = [
            'Reseat RAM',
            'Check Monitor & Cable',
            'Clear CMOS',
            'Replace Graphics Card'
        ]

        self.action_var = ctk.StringVar(value='Reseat RAM')

        for action in actions:
            rb = ctk.CTkRadioButton(panel, text=action, variable=self.action_var, value=action)
            rb.pack(anchor='w', padx=20, pady=5)

        ctk.CTkButton(
            panel,
            text='CONFIRM ACTION',
            height=45,
            command=self.confirm_action
        ).pack(fill='x', padx=20, pady=20)

    def confirm_action(self):
        selected = self.action_var.get()

        if selected == self.case['correct_action']:
            self.xp += 150
            messagebox.showinfo(
                'Correct Diagnosis',
                'Correct! RAM was not seated properly. System display restored.\\n\\n+150 XP earned.'
            )
        else:
            messagebox.showwarning(
                'Incorrect Action',
                f'"{selected}" did not resolve the issue. Continue troubleshooting.'
            )


if __name__ == '__main__':
    app = SmartFixApp()
    app.mainloop()