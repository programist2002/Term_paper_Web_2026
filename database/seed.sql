SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE products;
TRUNCATE TABLE categories;
TRUNCATE TABLE users;
SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO users (name, email, password, role) VALUES
('Адміністратор Магазину', 'admin@toyshop.local', {{ADMIN_PASSWORD_HASH}}, 'admin'),
('Ірина Коваль', 'iryna@toyshop.local', {{USER_IRYNA_PASSWORD_HASH}}, 'user'),
('Олег Мельник', 'oleh@toyshop.local', {{USER_OLEH_PASSWORD_HASH}}, 'user');

INSERT INTO categories (name, slug, description) VALUES
('М''які іграшки', 'miaki-ihrashky', 'Плюшеві друзі для затишних подарунків і дитячих кімнат.'),
('Конструктори', 'konstruktory', 'Набори для розвитку логіки, дрібної моторики та уяви.'),
('Настільні ігри', 'nastilni-ihry', 'Ігри для сімейних вечорів, компаній друзів та веселих зустрічей.'),
('Творчість', 'tvorchist', 'Набори для малювання, ліплення та інших творчих занять.');

INSERT INTO products (
    category_id,
    name,
    slug,
    manufacturer,
    image,
    short_description,
    description,
    price,
    quantity,
    is_available,
    sku
) VALUES
(
    (SELECT id FROM categories WHERE slug = 'miaki-ihrashky'),
    'Плюшевий ведмедик Bruno',
    'pliushevyi-vedmedyk-bruno',
    'Sweet Toys',
    'https://images.unsplash.com/photo-1515488042361-ee00e0ddd4e4?auto=format&fit=crop&w=900&q=80',
    'М''який ведмедик кремового кольору для дітей від 3 років.',
    'Великий плюшевий ведмедик Bruno стане улюбленцем дитини та затишним подарунком для будь-якого свята. Виготовлений із приємного на дотик гіпоалергенного матеріалу.',
    899.00,
    14,
    1,
    'PL-BRUNO-001'
),
(
    (SELECT id FROM categories WHERE slug = 'miaki-ihrashky'),
    'Плюшева лисичка Luna',
    'pliusheva-lysychka-luna',
    'Sweet Toys',
    'https://images.unsplash.com/photo-1545558014-8692077e9b5c?auto=format&fit=crop&w=900&q=80',
    'Яскрава лисичка для обіймів і декору дитячої кімнати.',
    'Лисичка Luna має м''яке наповнення, виразну мордочку та компактний розмір, що зручно брати із собою в подорож або садочок.',
    649.00,
    9,
    1,
    'PL-LUNA-002'
),
(
    (SELECT id FROM categories WHERE slug = 'konstruktory'),
    'Конструктор City Builders 120',
    'konstruktor-city-builders-120',
    'Brick Master',
    'https://images.unsplash.com/photo-1587654780291-39c9404d746b?auto=format&fit=crop&w=900&q=80',
    'Набір із 120 деталей для створення міського транспорту та будівель.',
    'Конструктор допомагає розвивати просторове мислення й навички моделювання. У комплекті кольорові блоки, колеса та інструкція з кількома варіантами складання.',
    1199.00,
    18,
    1,
    'KB-CITY-120'
),
(
    (SELECT id FROM categories WHERE slug = 'konstruktory'),
    'Конструктор Space Lab 240',
    'konstruktor-space-lab-240',
    'Brick Master',
    'https://images.unsplash.com/photo-1516979187457-637abb4f9353?auto=format&fit=crop&w=900&q=80',
    'Тематичний набір для збирання космічної станції.',
    'Space Lab 240 включає модулі станції, мініфігурки дослідників і додаткові елементи для сюжетної гри. Підійде для дітей від 8 років.',
    1890.00,
    7,
    1,
    'KB-SPACE-240'
),
(
    (SELECT id FROM categories WHERE slug = 'nastilni-ihry'),
    'Настільна гра Color Quest',
    'nastilna-hra-color-quest',
    'Fun Board',
    'https://images.unsplash.com/photo-1610890716171-6b1bb98ffd09?auto=format&fit=crop&w=900&q=80',
    'Швидка сімейна гра на увагу та логіку.',
    'Color Quest пропонує прості правила, яскраві картки та кілька режимів гри для дітей і дорослих. Чудовий вибір для компанії 2-5 гравців.',
    780.00,
    11,
    1,
    'NB-COLOR-015'
),
(
    (SELECT id FROM categories WHERE slug = 'nastilni-ihry'),
    'Настільна гра Dino Race',
    'nastilna-hra-dino-race',
    'Fun Board',
    'https://images.unsplash.com/photo-1606503153255-59d8b8bdfb2c?auto=format&fit=crop&w=900&q=80',
    'Весела гра-перегони з динозаврами та кубиками.',
    'Dino Race розрахована на сімейні ігрові сесії та поєднує елементи випадковості, тактики та яскравого візуального оформлення.',
    920.00,
    0,
    0,
    'NB-DINO-021'
),
(
    (SELECT id FROM categories WHERE slug = 'tvorchist'),
    'Набір для малювання Art Start',
    'nabir-dlia-maliuvannia-art-start',
    'Creative Box',
    'https://images.unsplash.com/photo-1513364776144-60967b0f800f?auto=format&fit=crop&w=900&q=80',
    'Фломастери, олівці та альбом у зручному кейсі.',
    'Art Start містить базовий набір для перших творчих експериментів: кольорові олівці, фломастери, воскові олівці та щільний папір для малювання.',
    560.00,
    20,
    1,
    'TV-ART-START'
),
(
    (SELECT id FROM categories WHERE slug = 'tvorchist'),
    'Набір для ліплення Clay Fun',
    'nabir-dlia-liplennia-clay-fun',
    'Creative Box',
    'https://images.unsplash.com/photo-1516321497487-e288fb19713f?auto=format&fit=crop&w=900&q=80',
    'Маса для ліплення та інструменти для творчих занять.',
    'Clay Fun допомагає дітям розвивати моторику й фантазію. У наборі кілька кольорів маси для ліплення, формочки та безпечні пластикові інструменти.',
    430.00,
    16,
    1,
    'TV-CLAY-008'
);