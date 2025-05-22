-- HaveToPay required database tables

-- Table for expenses
CREATE TABLE IF NOT EXISTS expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    description VARCHAR(255) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payer_id INT NOT NULL,
    group_id INT NULL,
    expense_date DATE NOT NULL,
    expense_category VARCHAR(50) NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (payer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Table for expense participants (who owes what)
CREATE TABLE IF NOT EXISTS expense_participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    expense_id INT NOT NULL,
    user_id INT NOT NULL,
    share_amount DECIMAL(10, 2) NOT NULL,
    is_settled TINYINT(1) DEFAULT 0,
    settled_date TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (expense_id) REFERENCES expenses(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_participation (expense_id, user_id)
) ENGINE=InnoDB;

-- Table for settlements (records of payments between users)
CREATE TABLE IF NOT EXISTS settlements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    payer_id INT NOT NULL,
    receiver_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    settlement_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (payer_id) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Table for expense categories
CREATE TABLE IF NOT EXISTS expense_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    icon VARCHAR(50) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_category_name (name)
) ENGINE=InnoDB;

-- Insert default expense categories
INSERT IGNORE INTO expense_categories (name, icon) VALUES 
('Food', 'fa-utensils'),
('Transportation', 'fa-car'),
('Housing', 'fa-home'),
('Utilities', 'fa-bolt'),
('Entertainment', 'fa-film'),
('Shopping', 'fa-shopping-cart'),
('Travel', 'fa-plane'),
('Health', 'fa-medkit'),
('Other', 'fa-question-circle');
