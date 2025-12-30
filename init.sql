CREATE DATABASE IF NOT EXISTS vulnerable_social;
USE vulnerable_social;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    bio TEXT,
    profile_picture VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id),
    FOREIGN KEY (receiver_id) REFERENCES users(id)
);

INSERT INTO users (username, password, email, bio) VALUES
('admin', 'password123', 'admin@vulnerable.com', 'System administrator'),
('alice', 'alice123', 'alice@example.com', 'Love cats and coffee'),
('bob', 'bob123', 'bob@example.com', 'Tech enthusiast and gamer'),
('charlie', 'charlie123', 'charlie@example.com', 'Musician and artist');

INSERT INTO posts (user_id, content) VALUES
(1, 'Welcome to our vulnerable social media platform!'),
(2, 'Just adopted a new kitten! 🐱'),
(3, 'Working on a new coding project'),
(4, 'Check out my latest song!');
