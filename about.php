<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - The Development Team</title>
    <?php include '../fooddelivery/includes/header.php'; ?>
</head>

<body>
    <?php
    $teamMembers = [
        [
            'name' => 'Tran Tien Hung',
            'position' => 'Team Leader & Full-Stack Developer',
            'avatar' => 'TTH',
            'description' => 'With over 2 years of experience in web development, Hung is responsible for leading the project and developing key features.',
            'skills' => ['PHP', 'JavaScript', 'HTML', 'MySQL']
        ],
        [
            'name' => 'Nguyen Tien Duong',
            'position' => 'Frontend Developer',
            'avatar' => 'NTD',
            'description' => 'A user interface expert with the ability to design beautiful UI/UX and provide a great user experience.',
            'skills' => ['HTML/CSS', 'JavaScript', 'Vue.js', 'Sass', 'Figma']
        ],
        [
            'name' => 'Doan Minh Duc',
            'position' => 'Backend Developer',
            'avatar' => 'DMD',
            'description' => 'Specializes in backend development, database management, and system performance optimization.',
            'skills' => ['PHP', 'Node.js', 'MongoDB', 'Redis', 'AWS']
        ],
        [
            'name' => 'Nguyen Duy Khoa',
            'position' => 'UI/UX Designer',
            'avatar' => 'NDK',
            'description' => 'Designs the user interface and experience, ensuring the product is aesthetically pleasing and easy to use.',
            'skills' => ['Photoshop', 'Illustrator', 'Sketch', 'InVision', 'Prototype']
        ],
        [
            'name' => 'Hoang Ngoc Anh',
            'position' => 'DevOps Engineer',
            'avatar' => 'HNA',
            'description' => 'Manages infrastructure, deploys applications, and ensures the system operates stably 24/7.',
            'skills' => ['Docker', 'Kubernetes', 'Jenkins', 'Linux', 'Monitoring']
        ],
        [
            'name' => 'Nguyen Le Minh',
            'position' => 'Quality Assurance',
            'avatar' => 'NLM',
            'description' => 'Ensures product quality through automated and manual testing, detecting and reporting bugs.',
            'skills' => ['Manual Testing', 'Selenium', 'Postman', 'Bug Tracking', 'Test Planning']
        ]
    ];
    ?>

    <div class="container">
        <div class="header">
            <h1>About Us</h1>
            <p>A professional development team with a passion for technology</p>
        </div>

        <div class="team-grid">
            <?php foreach ($teamMembers as $member): ?>
                <div class="team-member">
                    <div class="avatar"><?php echo $member['avatar']; ?></div>
                    <div class="member-name"><?php echo $member['name']; ?></div>
                    <div class="member-position"><?php echo $member['position']; ?></div>
                    <div class="member-description"><?php echo $member['description']; ?></div>
                    <div class="member-skills">
                        <?php foreach ($member['skills'] as $skill): ?>
                            <span class="skill-tag"><?php echo $skill; ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Arial', sans-serif;
        line-height: 1.6;
        color: #000;
        /* black text */
        background-color: #fff;
        /* white background */
        min-height: 100vh;
    }



    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .header {
        text-align: center;
        margin-bottom: 50px;
        color: white;
    }

    .header h1 {
        font-size: 3rem;
        margin-bottom: 10px;
        color: #ff6600;
        /* orange color for the title */
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
    }

    .header p {
        font-size: 1.2rem;
        color: #000;
        /* black color for the description */
        opacity: 1;
        /* make text more visible */
    }



    .team-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        margin-bottom: 50px;
    }

    .team-member {
        background: white;
        border-radius: 15px;
        padding: 30px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .team-member:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    }

    .avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: linear-gradient(45deg, #667eea, #764ba2);
        margin: 0 auto 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        color: white;
        font-weight: bold;
    }

    .member-name {
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 10px;
        color: #333;
    }

    .member-position {
        font-size: 1.1rem;
        color: #667eea;
        margin-bottom: 15px;
        font-weight: 600;
    }

    .member-description {
        color: #666;
        line-height: 1.5;
        margin-bottom: 20px;
    }

    .member-skills {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: center;
    }

    .skill-tag {
        background: #f0f2ff;
        color: #667eea;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .company-info {
        background: white;
        border-radius: 15px;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .company-info h2 {
        color: #333;
        margin-bottom: 20px;
        font-size: 2rem;
    }

    .company-info p {
        color: #666;
        font-size: 1.1rem;
        line-height: 1.8;
        margin-bottom: 15px;
    }

    @media (max-width: 768px) {
        .header h1 {
            font-size: 2rem;
        }

        .team-grid {
            grid-template-columns: 1fr;
        }

        .container {
            padding: 15px;
        }
    }
</style>

</html>
<?php include '/xampp/htdocs/PHP/fooddelivery/includes/footer.php'; ?>