-- ایجاد دیتابیس جدید
CREATE DATABASE IF NOT EXISTS blog_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE blog_db;
-- جدول کاربران (ساده شده)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- جدول تنظیمات
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    keywords TEXT,
    description TEXT,
    author VARCHAR(100),
    logo VARCHAR(255),
    footer TEXT
);
-- جدول پست‌ها
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    category ENUM('sport', 'social', 'political') NOT NULL,
    image VARCHAR(255),
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
-- درج کاربر ادمین
-- رمز عبور: admin123 (با bcrypt هش شده)
-- برای تولید هش جدید: password_hash('your_password', PASSWORD_DEFAULT)
INSERT INTO users (first_name, last_name, email, password)
VALUES ('Admin', 'User', 'admin@blog.com', '$2y$10$bVcWC0SGJb9A8vKzObtgTehWjbNiQJNZLfouJA/9vnqKH9xEm3pzu');
-- درج تنظیمات
INSERT INTO settings (
        title,
        keywords,
        description,
        author,
        logo,
        footer
    )
VALUES (
        'Gitmag Website',
        'news,sport,political,social',
        'A complete blog system with PHP',
        'sohrab azinfar',
        'images/logo.png',
        'Copyright 2024 Gitmag Website. All rights reserved.'
    );
-- درج 10 پست نمونه با IDهای 1 تا 10
INSERT INTO posts (id, title, content, category, image, views)
VALUES (
        1,
        'Economic Recovery Shows Strong Momentum',
        'Recent economic indicators suggest a robust recovery is underway across major economies. Employment numbers have improved significantly, consumer spending has increased, and business confidence is at its highest level in years. Central banks are carefully monitoring inflation pressures while maintaining supportive monetary policies to ensure sustainable growth. This positive trend is expected to continue through the next quarter, with analysts predicting increased investment in technology and infrastructure projects.',
        'political',
        'images/1.jpg',
        437
    ),
    (
        2,
        'New Sports Complex Opens in City Center',
        'The new state-of-the-art sports complex opened its doors today, featuring Olympic-sized swimming pools, indoor courts, and fitness facilities. The project, which took three years to complete, is expected to promote healthy living and provide world-class training facilities for local athletes. Community members expressed excitement about the new facilities, which include a running track, basketball courts, and a dedicated area for youth sports programs.',
        'sport',
        'images/2.jpg',
        289
    ),
    (
        3,
        'Community Garden Initiative Brings Neighborhoods Together',
        'Local residents have transformed vacant lots into thriving community gardens, creating green spaces that foster social connections and provide fresh produce. The initiative has reduced food insecurity and strengthened community bonds across the city. Volunteers work together to maintain the gardens, sharing knowledge about sustainable agriculture and organic farming practices while creating beautiful green spaces for everyone to enjoy.',
        'social',
        'images/3.jpg',
        156
    ),
    (
        4,
        'Political Summit Addresses Climate Change',
        'World leaders gathered for an emergency summit on climate change, announcing new commitments to reduce carbon emissions and invest in renewable energy. The agreements mark a significant step forward in global cooperation on environmental issues. Delegates from over 100 countries participated in the discussions, which focused on implementing practical solutions to address the urgent climate crisis facing our planet.',
        'political',
        'images/4.jpg',
        512
    ),
    (
        5,
        'Youth Soccer Team Wins National Championship',
        'The under-16 soccer team from our city defeated the defending champions in a thrilling final match. The victory marks the first national championship for the team and has inspired young athletes across the region. The teams exceptional performance throughout the tournament demonstrated their dedication and skill, with the final match going into overtime before our local heroes secured their well-deserved victory.',
        'sport',
        'images/5.jpg',
        198
    ),
    (
        6,
        'Local Artists Transform Urban Landscape with Murals',
        'A group of talented local artists has begun transforming dull urban walls into vibrant murals that tell the story of our community. The project, funded by the citys cultural department, aims to beautify public spaces and support local artists. Each mural reflects aspects of our citys history, diversity, and aspirations, creating open-air galleries that residents and visitors can enjoy throughout the city.',
        'social',
        'images/6.jpg',
        324
    ),
    (
        7,
        'Technology Conference Draws International Experts',
        'The annual technology conference attracted experts from around the world to discuss the latest innovations in artificial intelligence, blockchain, and sustainable technology. The event featured workshops, panel discussions, and networking opportunities for professionals and students alike. Keynote speakers shared insights about the future of technology and its impact on various industries, highlighting emerging trends and career opportunities.',
        'political',
        'images/7.jpg',
        267
    ),
    (
        8,
        'Marathon Raises Funds for Childrens Hospital',
        'Thousands of runners participated in the annual city marathon, raising significant funds for the local childrens hospital. The event brought together professional athletes, amateur runners, and community supporters for a day of fitness and philanthropy. Beyond the financial contributions, the event raised awareness about the hospitals needs and celebrated the spirit of community support that defines our city.',
        'sport',
        'images/8.jpg',
        189
    ),
    (
        9,
        'New Library Program Promotes Digital Literacy',
        'The city library has launched an innovative program to improve digital literacy among senior citizens and underserved communities. The initiative provides free classes on using smartphones, computers, and online resources safely and effectively. Participants have reported increased confidence in using technology for everyday tasks, from online banking to connecting with family members through video calls and social media.',
        'social',
        'images/9.jpg',
        233
    ),
    (
        10,
        'Historic Building Preservation Project Completed',
        'The extensive restoration of the citys historic landmark building has been completed, preserving an important piece of our architectural heritage for future generations. The project involved expert craftsmen using traditional techniques and materials to maintain the buildings original character while updating its infrastructure for modern use. The restored building will serve as a cultural center and museum, hosting exhibitions and educational programs about our citys rich history.',
        'political',
        'images/10.jpg',
        178
    );