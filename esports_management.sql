-- Creating the database
CREATE DATABASE esports_tournament;

USE esports_tournament;

-- Table for games (e.g., Valorant, League of Legends)
CREATE TABLE games (
    game_id INT PRIMARY KEY AUTO_INCREMENT,
    game_name VARCHAR(100) NOT NULL,
    genre VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for tournaments
CREATE TABLE tournaments (
    tournament_id INT PRIMARY KEY AUTO_INCREMENT,
    tournament_name VARCHAR(100) NOT NULL,
    game_id INT,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    prize_pool DECIMAL(10, 2),
    location VARCHAR(100),
    status ENUM('upcoming', 'ongoing', 'completed') DEFAULT 'upcoming',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (game_id) REFERENCES games(game_id) ON DELETE SET NULL
);

-- Table for teams
CREATE TABLE teams (
    team_id INT PRIMARY KEY AUTO_INCREMENT,
    team_name VARCHAR(100) NOT NULL,
    game_id INT,
    coach_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (game_id) REFERENCES games(game_id) ON DELETE SET NULL
);

-- Table for players
CREATE TABLE players (
    player_id INT PRIMARY KEY AUTO_INCREMENT,
    team_id INT,
    player_name VARCHAR(100) NOT NULL,
    in_game_name VARCHAR(50) NOT NULL,
    role VARCHAR(50),
    email VARCHAR(100) UNIQUE,
    date_of_birth DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (team_id) REFERENCES teams(team_id) ON DELETE SET NULL
);

-- Table for sponsors
CREATE TABLE sponsors (
    sponsor_id INT PRIMARY KEY AUTO_INCREMENT,
    sponsor_name VARCHAR(100) NOT NULL,
    contact_email VARCHAR(100),
    sponsorship_amount DECIMAL(10, 2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for tournament matches
CREATE TABLE matches (
    match_id INT PRIMARY KEY AUTO_INCREMENT,
    tournament_id INT,
    team1_id INT NOT NULL,
    team2_id INT NOT NULL,
    match_date DATETIME NOT NULL,
    stage VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tournament_id) REFERENCES tournaments(tournament_id) ON DELETE CASCADE,
    FOREIGN KEY (team1_id) REFERENCES teams(team_id) ON DELETE CASCADE,
    FOREIGN KEY (team2_id) REFERENCES teams(team_id) ON DELETE CASCADE
);

-- Table for match results
CREATE TABLE match_results (
    result_id INT PRIMARY KEY AUTO_INCREMENT,
    match_id INT,
    winner_id INT,
    team1_score INT NOT NULL,
    team2_score INT NOT NULL,
    match_duration TIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (match_id) REFERENCES matches(match_id) ON DELETE CASCADE,
    FOREIGN KEY (winner_id) REFERENCES teams(team_id) ON DELETE SET NULL
);

-- Insert sample data for games
INSERT INTO games (game_name, genre) VALUES
('Valorant', 'FPS'),
('League of Legends', 'MOBA'),
('Dota 2', 'MOBA'),
('Counter-Strike 2', 'FPS'),
('Fortnite', 'Battle Royale');

-- Insert sample data for tournaments
INSERT INTO tournaments (tournament_name, game_id, start_date, end_date, prize_pool, location, status) VALUES
('Valorant Champions 2023', 1, '2023-08-15', '2023-08-30', 1000000.00, 'Los Angeles', 'completed'),
('LoL World Championship', 2, '2023-10-05', '2023-11-05', 2500000.00, 'Seoul', 'completed'),
('Dota 2 The International', 3, '2023-10-20', '2023-10-30', 4000000.00, 'Singapore', 'completed'),
('CS2 Major Paris', 4, '2024-05-15', '2024-05-30', 1500000.00, 'Paris', 'upcoming'),
('Fortnite World Cup', 5, '2024-07-20', '2024-07-25', 3000000.00, 'New York', 'upcoming');

-- Insert sample data for teams
INSERT INTO teams (team_name, game_id, coach_name) VALUES
('Sentinels', 1, 'Shane "Rawkus" Flaherty'),
('Fnatic', 1, 'Jacob "mini" Harris'),
('T1', 2, 'Bae "Bengi" Seong-woong'),
('G2 Esports', 2, 'Dylan Falco'),
('Team Spirit', 3, 'Dmitriy "Larl" Karpov'),
('Natus Vincere', 4, 'Andrey "B1ad3" Gorodenskiy'),
('FaZe Clan', 4, 'Filip "NEO" Kubski'),
('TSM', 5, 'Connor "Co1azo" Colazo');

-- Insert sample data for players
INSERT INTO players (team_id, player_name, in_game_name, role, email, date_of_birth) VALUES
(1, 'Tyson Ngo', 'TenZ', 'Duelist', 'tenz@sentinels.gg', '2001-05-05'),
(1, 'Shahzeb Khan', 'ShahZaM', 'Initiator', 'shahzam@sentinels.gg', '1994-10-08'),
(2, 'Jake Howlett', 'Boaster', 'Initiator', 'boaster@fnatic.gg', '1995-05-25'),
(2, 'Nikita Sirmitev', 'Derke', 'Duelist', 'derke@fnatic.gg', '2002-02-06'),
(3, 'Lee Sang-hyeok', 'Faker', 'Mid', 'faker@t1.gg', '1996-05-07'),
(3, 'Bae Seong-woong', 'Bengi', 'Jungle', 'bengi@t1.gg', '1993-11-21'),
(4, 'Rasmus Winther', 'Caps', 'Mid', 'caps@g2.gg', '1999-11-17'),
(5, 'Illya Mulyarchuk', 'Yatoro', 'Carry', 'yatoro@teamspirit.gg', '2003-06-12'),
(6, 'Oleksandr Kostyliev', 's1mple', 'AWPer', 's1mple@navi.gg', '1997-10-02'),
(7, 'Håvard Nygaard', 'rain', 'Rifler', 'rain@faze.gg', '1994-08-26'),
(8, 'Kyle Giersdorf', 'Bugha', 'Solo', 'bugha@tsm.gg', '2002-12-30');

-- Insert sample data for sponsors
INSERT INTO sponsors (sponsor_name, contact_email, sponsorship_amount) VALUES
('Red Bull', 'esports@redbull.com', 500000.00),
('Logitech', 'sponsorships@logitech.com', 250000.00),
('Intel', 'gaming@intel.com', 750000.00),
('NVIDIA', 'sponsors@nvidia.com', 600000.00),
('Secretlab', 'partners@secretlab.com', 150000.00);

-- Insert sample data for matches
INSERT INTO matches (tournament_id, team1_id, team2_id, match_date, stage) VALUES
(1, 1, 2, '2023-08-16 14:00:00', 'Group Stage'),
(1, 1, 2, '2023-08-20 18:00:00', 'Quarterfinal'),
(2, 3, 4, '2023-10-10 12:00:00', 'Group Stage'),
(3, 5, 5, '2023-10-22 15:00:00', 'Semifinal'),
(4, 6, 7, '2024-05-16 16:00:00', 'Group Stage'),
(5, 8, 8, '2024-07-21 13:00:00', 'Final');

-- Insert sample data for match results
INSERT INTO match_results (match_id, winner_id, team1_score, team2_score, match_duration) VALUES
(1, 1, 13, 9, '00:45:23'),
(2, 2, 10, 13, '00:52:17'),
(3, 3, 1, 0, '01:15:42'),
(4, 5, 2, 1, '02:08:35'),
(5, NULL, 0, 0, '00:00:00'),
(6, NULL, 0, 0, '00:00:00');