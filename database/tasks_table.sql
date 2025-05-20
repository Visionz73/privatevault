CREATE TABLE IF NOT EXISTS `tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `status` varchar(20) NOT NULL DEFAULT 'todo',
  `priority` varchar(10) DEFAULT 'medium',
  `due_date` date DEFAULT NULL,
  `assignee` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add some sample data
INSERT INTO `tasks` (`title`, `description`, `status`, `priority`, `due_date`, `assignee`) VALUES
('Website Redesign', 'Update the company website with new branding elements', 'todo', 'high', '2023-12-15', 'John'),
('Database Optimization', 'Improve query performance for the user management module', 'inprogress', 'medium', '2023-12-10', 'Anna'),
('Bug Fix: Login Page', 'Fix the issue with remember me functionality', 'todo', 'high', '2023-12-05', 'Mike'),
('Documentation Update', 'Write user guide for the new features', 'inprogress', 'low', '2023-12-20', NULL),
('Server Migration', 'Migrate application to new cloud infrastructure', 'todo', 'medium', '2023-12-25', 'Sarah'),
('Security Audit', 'Perform security review on authentication module', 'completed', 'high', '2023-12-01', 'David');
