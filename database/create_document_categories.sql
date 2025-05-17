-- Create categories table
CREATE TABLE IF NOT EXISTS document_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE
);

-- Add category_id to documents table
ALTER TABLE documents 
ADD COLUMN category_id INT,
ADD FOREIGN KEY (category_id) REFERENCES document_categories(id);

-- Insert predefined categories
INSERT INTO document_categories (name) VALUES 
('Verträge'),
('Versicherungen'),
('Rechnungen'),
('Sonstige');
