<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Về Chúng Tôi - Đội Ngũ Phát Triển</title>
    <?php include '../fooddelivery/includes/header.php'; ?>
</head>

<body>
    <?php
    $teamMembers = [
        [
            'name' => 'Trần Tiến Hưng',
            'position' => 'Team Leader & Full-Stack Developer',
            'avatar' => 'TTH',
            'description' => 'Với hơn 2 năm kinh nghiệm trong phát triển web, An chịu trách nhiệm lãnh đạo dự án và phát triển các tính năng chính.',
            'skills' => ['PHP', 'JavaScript', 'HTML', 'MySQL']
        ],
        [
            'name' => 'Nguyễn Tiến Dương',
            'position' => 'Frontend Developer',
            'avatar' => 'NTD',
            'description' => 'Chuyên gia về giao diện người dùng với khả năng thiết kế UI/UX đẹp mắt và trải nghiệm người dùng tốt.',
            'skills' => ['HTML/CSS', 'JavaScript', 'Vue.js', 'Sass', 'Figma']
        ],
        [
            'name' => 'Đoàn Minh Đức',
            'position' => 'Backend Developer',
            'avatar' => 'DMD',
            'description' => 'Chuyên về phát triển backend, quản lý cơ sở dữ liệu và tối ưu hóa hiệu suất hệ thống.',
            'skills' => ['PHP', 'Node.js', 'MongoDB', 'Redis', 'AWS']
        ],
        [
            'name' => 'Nguyễn Duy Khoa',
            'position' => 'UI/UX Designer',
            'avatar' => 'NDK',
            'description' => 'Thiết kế giao diện và trải nghiệm người dùng, đảm bảo sản phẩm có tính thẩm mỹ cao và dễ sử dụng.',
            'skills' => ['Photoshop', 'Illustrator', 'Sketch', 'InVision', 'Prototype']
        ],
        [
            'name' => 'Hoàng Ngọc Anh',
            'position' => 'DevOps Engineer',
            'avatar' => 'HNA',
            'description' => 'Quản lý hạ tầng, triển khai ứng dụng và đảm bảo hệ thống hoạt động ổn định 24/7.',
            'skills' => ['Docker', 'Kubernetes', 'Jenkins', 'Linux', 'Monitoring']
        ],
        [
            'name' => 'Nguyễn Lê Minh',
            'position' => 'Quality Assurance',
            'avatar' => 'NLM',
            'description' => 'Đảm bảo chất lượng sản phẩm thông qua kiểm thử tự động và thủ công, phát hiện và báo cáo lỗi.',
            'skills' => ['Manual Testing', 'Selenium', 'Postman', 'Bug Tracking', 'Test Planning']
        ]
    ];
    ?>

    <div class="container">
        <div class="header">
            <h1>Về Chúng Tôi</h1>
            <p>Đội ngũ phát triển chuyên nghiệp với niềm đam mê công nghệ</p>
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
        /* chữ đen */
        background-color: #fff;
        /* nền trắng */
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
        /* màu cam cho tiêu đề */
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
    }

    .header p {
        font-size: 1.2rem;
        color: #000;
        /* màu đen cho mô tả */
        opacity: 1;
        /* hiện rõ chữ hơn */
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