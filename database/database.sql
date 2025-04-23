CREATE DATABASE quiz_app

CREATE  TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR (100) NOT NULL,
    email VARCHAR (100) NOT NULL UNIQUE,
    password VARCHAR(200) NOT NULL,
    role VARCHAR(10) DEFAULT 'user'
);

CREATE TABLE quizzes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );

CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT NOT NULL,
    question_text TEXT NOT NULL,
    option_a VARCHAR(255) NOT NULL,
    option_b VARCHAR(255) NOT NULL,
    option_c VARCHAR(255) NOT NULL,
    option_d VARCHAR(255) NOT NULL,
    correct_answer VARCHAR(1) NOT NULL,
    question_order INT DEFAULT 1,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id)
    );
    


CREATE TABLE scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    quiz_id INT NOT NULL,
    score INT NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id)
    );