    // Navbar scroll
    window.addEventListener('scroll', function(){
        const navbar = document.getElementById('navbar');
        if (window.scrollY > 50) navbar.classList.add('scrolled');
        else navbar.classList.remove('scrolled');
    });

// Countdown
function updateCountdown(){
    const eventDate = new Date('December 26, 2026 09:00:00').getTime();
    const now = new Date().getTime();
    const distance = eventDate - now;
    
    if (distance <= 0) {
        document.getElementById('days').textContent = '0';
        document.getElementById('hours').textContent = '0';
        document.getElementById('minutes').textContent = '0';
        document.getElementById('seconds').textContent = '0';
        return;
    }
    
    const d = Math.floor(distance / (1000*60*60*24));
    const h = Math.floor((distance % (1000*60*60*24))/(1000*60*60));
    const m = Math.floor((distance % (1000*60*60))/(1000*60));
    const s = Math.floor((distance % (1000*60))/1000);
    
    document.getElementById('days').textContent = d;
    document.getElementById('hours').textContent = h;
    document.getElementById('minutes').textContent = m;
    document.getElementById('seconds').textContent = s;
}

setInterval(updateCountdown, 1000);
updateCountdown();

    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(a=>{
        a.addEventListener('click',e=>{
            const href = a.getAttribute('href');
            if (href.length > 1){
                e.preventDefault();
                document.querySelector(href).scrollIntoView({behavior:'smooth'});
            }
        });
    });

    // Simple form handler
    document.getElementById('registrationForm').addEventListener('submit',e=>{
        e.preventDefault();
        alert(currentLang === 'ru'
            ? 'Спасибо за регистрацию! Мы свяжемся с вами по email.'
            : currentLang === 'tj'
                ? 'Ташаккур барои бақайдгирӣ! Мо тавассути почтаи электронӣ бо шумо тамос мегирем.'
                : 'Thank you for registering! We will contact you by email.'
        );
    });

    const translations = {
        en: {
            meta_title: "International Coal Forum Tajikistan 2026",

            nav_about:"About",
            nav_topics:"Topics",
            nav_agenda:"Agenda",
            nav_speakers:"Speakers",
            nav_pricing:"Pricing",
            nav_venue:"Venue",
            nav_register:"Register Now",

            hero_badge:"Central Asia's Premier Mining Event",
            hero_title:"International Coal Forum Tajikistan 2026",
            hero_subtitle:"\"Shaping the Future of Central Asian Coal: Growth, Innovation & Sustainable Development\"",
            hero_date:"December 26, 2026",
            hero_place:"Dushanbe, Tajikistan",
            hero_cta:"Register Now",
            hero_learn_more:"Learn More",

            cd_days:"Days",
            cd_hours:"Hours",
            cd_minutes:"Minutes",
            cd_seconds:"Seconds",

            about_title:"About The Forum",
            about_text:"Join 500+ industry leaders, government officials, and investors at Central Asia's most influential coal and mining conference",
            about_card1_title:"Market Insights",
            about_card1_text:"Gain exclusive access to the latest market data, forecasts, and analysis from leading industry experts",
            about_card2_title:"Networking",
            about_card2_text:"Connect with decision-makers from mining companies, government bodies, and financial institutions",
            about_card3_title:"Innovation",
            about_card3_text:"Discover cutting-edge technologies and sustainable practices transforming the coal industry",
            about_card4_title:"Investment",
            about_card4_text:"Explore multibillion-dollar energy and mining opportunities in Tajikistan and Central Asia",

            stat_attendees:"Attendees",
            stat_speakers:"Speakers",
            stat_countries:"Countries",
            stat_reserves:"Tonnes of coal reserves",

            topics_title:"Key Discussion Topics",
            topics_text:"Comprehensive coverage of the most important trends and challenges in the coal and energy sector",
            topic1_title:"Coal Market Outlook 2026–2030",
            topic1_text:"Supply, demand and price scenarios for Central Asia and key export markets",
            topic2_title:"Mining Strategy & Regulation",
            topic2_text:"Licensing, safety, and environmental requirements for new projects",
            topic3_title:"Industrial Coal Use",
            topic3_text:"Cement, metallurgy, and industrial consumers in Tajikistan and the region",
            topic4_title:"Power Generation",
            topic4_text:"Coal-based power projects and integration with hydropower",
            topic5_title:"Investment & Finance",
            topic5_text:"Structuring projects, risk mitigation, and access to capital",
            topic6_title:"Sustainability & ESG",
            topic6_text:"Cleaner technologies, rehabilitation of coal regions, and community development",

            agenda_title:"Forum Agenda",
            agenda_text:"Three days of strategic discussions, case studies, and networking",
            agenda_day1:"Day 1 – March 26",
            agenda1_1_title:"Registration & Welcome Coffee",
            agenda1_1_text:"Badge collection and informal networking",
            agenda1_2_title:"Opening Ceremony & Ministerial Address",
            agenda1_2_text:"Official welcome from government representatives",
            agenda1_3_title:"Keynote: Tajikistan's Coal Vision 2030",
            agenda1_3_text:"Long-term strategy for coal and mining sector development",
            agenda1_4_title:"Panel: Global Coal Market Dynamics",
            agenda1_4_text:"International experts share perspectives on demand and trade flows",
            agenda1_5_title:"Networking Reception",
            agenda1_5_text:"Evening reception for business introductions and deal-making",

            speakers_title:"Featured Speakers",
            speakers_text:"Hear from senior government officials, industry executives, and financial institutions",
            speaker1_name:"Senior Official",
            speaker1_role:"Government Policy",
            speaker1_org:"Ministry of Industry & New Technologies",
            speaker2_name:"Energy Leader",
            speaker2_role:"Energy Integration",
            speaker2_org:"Ministry of Energy & Water Resources",
            speaker3_name:"Mining Executive",
            speaker3_role:"Operations & Technology",
            speaker3_org:"International Mining Company",
            speaker4_name:"Investment Director",
            speaker4_role:"Finance & Deals",
            speaker4_org:"Regional Development Bank",

            pricing_title:"Registration Packages",
            pricing_text:"Early bird rates apply until 15 February 2026",
            price_virtual_title:"Virtual Access",
            price_virtual_old:"$200",
            price_virtual_new:"$150",
            price_virtual_f1:"Live streaming of all sessions",
            price_virtual_f2:"Digital conference materials",
            price_virtual_f3:"Virtual networking tools",
            price_virtual_f4:"Access to recordings",
            price_full_title:"Full Access",
            price_full_old:"$600",
            price_full_new:"$450",
            price_full_f1:"All sessions and workshops",
            price_full_f2:"Coffee breaks and lunches",
            price_full_f3:"Networking events",
            price_full_f4:"Gala dinner",
            price_full_f5:"Site visit (optional)",
            price_vip_title:"VIP Delegation",
            price_vip_new:"$1,200",
            price_vip_f1:"All Full Access benefits",
            price_vip_f2:"Premium front-row seating",
            price_vip_f3:"VIP lounge and concierge",
            price_vip_f4:"Exclusive roundtables",
            price_vip_f5:"Airport transfer",
            price_best:"Most Popular",
            price_select:"Select",

            reg_title:"Register Now",
            reg_text:"Complete the form below to secure your place at the forum",
            reg_first_name:"First Name *",
            reg_last_name:"Last Name *",
            reg_email:"Email *",
            reg_phone:"Phone *",
            reg_company:"Company / Organization *",
            reg_job:"Job Title *",
            reg_country:"Country *",
            reg_sector:"Industry Sector *",
            reg_package:"Registration Package *",
            reg_days:"Days Attending *",
            reg_day1:"Day 1 – March 26",
            reg_diet:"Dietary requirements",
            reg_diet_none:"No restrictions",
            reg_diet_veg:"Vegetarian",
            reg_diet_halal:"Halal",
            reg_diet_other:"Other",
            reg_visa:"Visa support letter needed?",
            reg_visa_no:"No",
            reg_visa_yes:"Yes",
            reg_comments:"Special requests / comments",
            reg_terms:"I agree to the Terms & Conditions and Privacy Policy *",
            reg_newsletter:"I agree to receive event updates by email",
            reg_submit:"Submit Registration",

            sector_mining:"Mining & Extraction",
            sector_energy:"Energy & Power",
            sector_equipment:"Equipment & Technology",
            sector_logistics:"Transportation & Logistics",
            sector_government:"Government & Regulation",
            sector_finance:"Finance & Investment",
            sector_academia:"Research & Academia",
            sector_media:"Media & Press",
            sector_other:"Other",

            reg_pkg_virtual:"Virtual Access",
            reg_pkg_full:"Full Access",
            reg_pkg_gov:"Government / Academic",
            reg_pkg_vip:"VIP Delegation",
            reg_pkg_booth:"Exhibition Booth",

            venue_title:"Venue & Location",
            venue_name:"Hyatt Regency Dushanbe",
            venue_addr:"26/1 Ismoili Somoni Avenue, Dushanbe, Tajikistan",
            venue_airport:"15 minutes from Dushanbe International Airport",
            venue_hotel:"5-star accommodation with special rates for delegates",
            venue_food:"High-quality catering and coffee breaks",
            venue_wifi:"High-speed Wi-Fi throughout the venue",

            partners_title:"Partners & Sponsors",
            partner1:"Strategic Partner",
            partner2:"Gold Sponsor",
            partner3:"Silver Sponsor",
            partner4:"Industry Partner",
            partner5:"Media Partner",

            footer_about_title:"International Coal Forum",
            footer_about_text:"Central Asia's leading platform for the coal and mining industry.",
            footer_links_title:"Quick Links",
            footer_link_about:"About",
            footer_link_agenda:"Agenda",
            footer_link_speakers:"Speakers",
            footer_link_pricing:"Registration",
            footer_link_register:"Register",
            footer_contact_title:"Contact",
            footer_org_title:"Organized by",
            footer_org_text:"Ministry of Industry and New Technologies of the Republic of Tajikistan.",
            footer_copy:"© 2026 International Coal Forum Tajikistan. All rights reserved."
        },
        ru: {
            meta_title:"Международный угольный форум Таджикистан 2026",

            nav_about:"О форуме",
            nav_topics:"Темы",
            nav_agenda:"Программа",
            nav_speakers:"Спикеры",
            nav_pricing:"Участие",
            nav_venue:"Место",
            nav_register:"Регистрация",

            hero_badge:"Главное угольное событие Центральной Азии",
            hero_title:"Международный угольный форум Таджикистан 2026",
            hero_subtitle:"\"Формируя будущее угольной отрасли Центральной Азии: рост, инновации и устойчивое развитие\"",
            hero_date:"26–28 марта 2026",
            hero_place:"Душанбе, Таджикистан",
            hero_cta:"Зарегистрироваться",
            hero_learn_more:"Подробнее",

            cd_days:"Дней",
            cd_hours:"Часов",
            cd_minutes:"Минут",
            cd_seconds:"Секунд",

            about_title:"О Форуме",
            about_text:"Более 500 лидеров отрасли, представителей госструктур и инвесторов на ключевой площадке угольной и горнодобывающей индустрии региона.",
            about_card1_title:"Рыночная аналитика",
            about_card1_text:"Доступ к актуальным данным рынка, прогнозам и аналитике ведущих экспертов.",
            about_card2_title:"Нетворкинг",
            about_card2_text:"Контакты с руководителями компаний, госорганов и финансовых институтов.",
            about_card3_title:"Инновации",
            about_card3_text:"Современные технологии и устойчивые практики в угольной отрасли.",
            about_card4_title:"Инвестиции",
            about_card4_text:"Инвестиционные возможности в энергетике и добыче полезных ископаемых Таджикистана.",

            stat_attendees:"Участников",
            stat_speakers:"Спикеров",
            stat_countries:"Стран",
            stat_reserves:"Тонн запасов угля",

            topics_title:"Ключевые темы",
            topics_text:"Главные тренды и вызовы угольной и энергетической отрасли.",
            topic1_title:"Перспективы рынка угля 2026–2030",
            topic1_text:"Спрос, предложение и цены в Центральной Азии и за её пределами.",
            topic2_title:"Стратегия и регулирование",
            topic2_text:"Лицензирование, безопасность и экология для новых проектов.",
            topic3_title:"Промышленное использование угля",
            topic3_text:"Цемент, металлургия и другие промышленные потребители.",
            topic4_title:"Электроэнергетика",
            topic4_text:"Угольная генерация и интеграция с гидроэнергетикой.",
            topic5_title:"Инвестиции и финансирование",
            topic5_text:"Структурирование проектов, риски и привлечение капитала.",
            topic6_title:"Устойчивое развитие и ESG",
            topic6_text:"Экологичные технологии, рекультивация и работа с сообществами.",

            agenda_title:"Программа форума",
            agenda_text:"Три дня стратегических дискуссий, кейсов и встреч.",
            agenda_day1:"День 1 – 26 марта",
            agenda1_1_title:"Регистрация и приветственный кофе",
            agenda1_1_text:"Получение бейджей и первые знакомства.",
            agenda1_2_title:"Открытие форума и приветствие",
            agenda1_2_text:"Официальные выступления представителей правительства.",
            agenda1_3_title:"Пленарный доклад: видение угольной отрасли до 2030 года",
            agenda1_3_text:"Долгосрочная стратегия развития угольной и горнодобывающей отрасли.",
            agenda1_4_title:"Панель: глобальный рынок угля",
            agenda1_4_text:"Мировые эксперты о спросе, логистике и торговых потоках.",
            agenda1_5_title:"Вечерний приём и нетворкинг",
            agenda1_5_text:"Неофициальное общение и установление партнёрств.",

            speakers_title:"Спикеры",
            speakers_text:"Представители госорганов, бизнеса и финансовых институтов.",
            speaker1_name:"Высокопоставленный чиновник",
            speaker1_role:"Государственная политика",
            speaker1_org:"Министерство промышленности и новых технологий",
            speaker2_name:"Руководитель энергетического блока",
            speaker2_role:"Энергетика и интеграция",
            speaker2_org:"Министерство энергетики и водных ресурсов",
            speaker3_name:"Глава горнодобывающей компании",
            speaker3_role:"Производство и технологии",
            speaker3_org:"Международная горнодобывающая компания",
            speaker4_name:"Директор по инвестициям",
            speaker4_role:"Финансы и сделки",
            speaker4_org:"Региональный банк развития",

            pricing_title:"Пакеты участия",
            pricing_text:"Льготные тарифы действуют до 15 февраля 2026 года.",
            price_virtual_title:"Онлайн-участие",
            price_virtual_old:"$200",
            price_virtual_new:"$150",
            price_virtual_f1:"Прямые трансляции всех сессий",
            price_virtual_f2:"Цифровые материалы форума",
            price_virtual_f3:"Онлайн-платформа для общения",
            price_virtual_f4:"Доступ к записям выступлений",
            price_full_title:"Очное участие",
            price_full_old:"$600",
            price_full_new:"$450",
            price_full_f1:"Все сессии и мастер-классы",
            price_full_f2:"Кофе-брейки и обеды",
            price_full_f3:"Нетворкинг-мероприятия",
            price_full_f4:"Гала-ужин",
            price_full_f5:"Выезд на объект (по желанию)",
            price_vip_title:"VIP-делегация",
            price_vip_new:"$1,200",
            price_vip_f1:"Все опции очного участия",
            price_vip_f2:"Места в первых рядах",
            price_vip_f3:"VIP-зона и персональное сопровождение",
            price_vip_f4:"Закрытые круглые столы",
            price_vip_f5:"Трансфер из аэропорта",
            price_best:"Самый популярный",
            price_select:"Выбрать",

            reg_title:"Регистрация",
            reg_text:"Заполните форму, чтобы забронировать участие в форуме.",
            reg_first_name:"Имя *",
            reg_last_name:"Фамилия *",
            reg_email:"Email *",
            reg_phone:"Телефон *",
            reg_company:"Компания / организация *",
            reg_job:"Должность *",
            reg_country:"Страна *",
            reg_sector:"Сфера деятельности *",
            reg_package:"Пакет участия *",
            reg_days:"Дни участия *",
            reg_day1:"День 1 – 26 марта",
            reg_diet:"Пищевые предпочтения",
            reg_diet_none:"Без ограничений",
            reg_diet_veg:"Вегетарианское питание",
            reg_diet_halal:"Халал",
            reg_diet_other:"Другое",
            reg_visa:"Требуется визовая поддержка?",
            reg_visa_no:"Нет",
            reg_visa_yes:"Да",
            reg_comments:"Особые запросы / комментарии",
            reg_terms:"Я согласен с Условиями участия и Политикой конфиденциальности *",
            reg_newsletter:"Я согласен получать новости и обновления о форуме",
            reg_submit:"Отправить заявку",

            sector_mining:"Добыча и горнорудная промышленность",
            sector_energy:"Энергетика и генерация",
            sector_equipment:"Оборудование и технологии",
            sector_logistics:"Транспорт и логистика",
            sector_government:"Госорганы и регулирование",
            sector_finance:"Финансы и инвестиции",
            sector_academia:"Наука и образование",
            sector_media:"Медиа и прессa",
            sector_other:"Другое",

            reg_pkg_virtual:"Онлайн-участие",
            reg_pkg_full:"Очное участие",
            reg_pkg_gov:"Госструктуры / академия",
            reg_pkg_vip:"VIP-делегация",
            reg_pkg_booth:"Выставочный стенд",

            venue_title:"Место проведения",
            venue_name:"Hyatt Regency Dushanbe",
            venue_addr:"проспект Исмоили Сомони 26/1, Душанбе, Таджикистан",
            venue_airport:"15 минут от международного аэропорта Душанбе",
            venue_hotel:"5‑звёздочный отель со специальными тарифами для участников",
            venue_food:"Качественное питание и кофе-брейки",
            venue_wifi:"Высокоскоростной Wi‑Fi на всей территории",

            partners_title:"Партнёры и спонсоры",
            partner1:"Стратегический партнёр",
            partner2:"Золотой спонсор",
            partner3:"Серебряный спонсор",
            partner4:"Отраслевой партнёр",
            partner5:"Медиапартнёр",

            footer_about_title:"International Coal Forum",
            footer_about_text:"Ведущая площадка угольной и горнодобывающей отрасли Центральной Азии.",
            footer_links_title:"Быстрые ссылки",
            footer_link_about:"О форуме",
            footer_link_agenda:"Программа",
            footer_link_speakers:"Спикеры",
            footer_link_pricing:"Участие",
            footer_link_register:"Регистрация",
            footer_contact_title:"Контакты",
            footer_org_title:"Организатор",
            footer_org_text:"Министерство промышленности и новых технологий Республики Таджикистан.",
            footer_copy:"© 2026 Международный угольный форум Таджикистан. Все права защищены."
        },
        tj: {
            meta_title:"Форуми байналмилалии ангишт Тоҷикистон 2026",

            nav_about:"Дар бораи форум",
            nav_topics:"Мавзӯъҳо",
            nav_agenda:"Барнома",
            nav_speakers:"Суханронҳо",
            nav_pricing:"Ширкат",
            nav_venue:"Ҷойи баргузорӣ",
            nav_register:"Бақайдгирӣ",

            hero_badge:"Рӯйдоди асосии ангишт дар Осиёи Марказӣ",
            hero_title:"Форуми байналмилалии ангишт Тоҷикистон 2026",
            hero_subtitle:"\"Ояндаи соҳаи ангишти Осиёи Марказӣ: рушд, навоварӣ ва рушди устувор\"",
            hero_date:"26–28 марти 2026",
            hero_place:"шаҳри Душанбе, Тоҷикистон",
            hero_cta:"Бақайд гирифтан",
            hero_learn_more:"Маълумоти бештар",

            cd_days:"Рӯз",
            cd_hours:"Соат",
            cd_minutes:"Дақиқа",
            cd_seconds:"Сония",

            about_title:"Дар бораи форум",
            about_text:"Беш аз 500 роҳбари соҳа, намояндагони ҳукумат ва сармоягузорон дар майдони асосии ангишт ва кӯҳкорӣ дар минтақа.",
            about_card1_title:"Таҳлили бозор",
            about_card1_text:"Дастрасӣ ба маълумоти нав, пешгӯиҳо ва таҳлили коршиносони пешсаф.",
            about_card2_title:"Шабакасозӣ",
            about_card2_text:"Шиносоӣ бо қаболгирандагони қарор аз ширкатҳо, мақомот ва бонкҳо.",
            about_card3_title:"Навоварӣ",
            about_card3_text:"Технологияҳои муосир ва амалияҳои устувор дар соҳаи ангишт.",
            about_card4_title:"Сармоягузорӣ",
            about_card4_text:"Имкониятҳои сармоягузорӣ дар энергетика ва истихроҷи маъдан дар Тоҷикистон.",

            stat_attendees:"Иштирокчиён",
            stat_speakers:"Суханронҳо",
            stat_countries:"Кишварҳо",
            stat_reserves:"Тонна захираҳои ангишт",

            topics_title:"Мавзӯъҳои асосӣ",
            topics_text:"Тамоюлҳо ва масъалаҳои муҳими соҳаи ангишт ва энергетика.",
            topic1_title:"Назар ба бозори ангишт 2026–2030",
            topic1_text:"Талабот, пешниҳод ва арзиш дар Осиёи Марказӣ ва бозорҳои содиротӣ.",
            topic2_title:"Стратегия ва танзимкунӣ",
            topic2_text:"Литсензия, бехатарӣ ва талаботи экологӣ барои лоиҳаҳои нав.",
            topic3_title:"Истифодаи саноатии ангишт",
            topic3_text:"Саноати семент, металлургия ва дигар истифодабарандагони калон.",
            topic4_title:"Нерӯгоҳи барқӣ",
            topic4_text:"Лоиҳаҳои барқи ангиштӣ ва ҳамгироӣ бо гидроэнергетика.",
            topic5_title:"Сармоягузорӣ ва молия",
            topic5_text:"Сохтори лоиҳаҳо, идоракунии хавфҳо ва дастрасӣ ба сармоя.",
            topic6_title:"Устуворӣ ва ESG",
            topic6_text:"Технологияҳои тоза, барқарорсозии минтақаҳои ангиштӣ ва рушди ҷомеа.",

            agenda_title:"Барномаи форум",
            agenda_text:"Се рӯз мубодилаи таҷриба, баҳсҳо ва вохӯриҳои тиҷоратӣ.",
            agenda_day1:"Рӯз 1 – 26 март",
            agenda_day2:"Рӯз 2 – 27 март",
            agenda_day3:"Рӯз 3 – 28 март",
            agenda1_1_title:"Бақайдгирӣ ва қаҳваи истиқболӣ",
            agenda1_1_text:"Гирифтани нишондод ва шиносоии аввал.",
            agenda1_2_title:"Кушодашавии расмӣ ва суханронии мақомот",
            agenda1_2_text:"Пешвози расмӣ аз ҷониби намояндагони ҳукумат.",
            agenda1_3_title:"Маърӯзаи асосӣ: дидгоҳи соҳаи ангишт то соли 2030",
            agenda1_3_text:"Стратегияи дарозмуддати рушди соҳаи ангишт ва кӯҳкорӣ.",
            agenda1_4_title:"Панел: бозори ҷаҳонии ангишт",
            agenda1_4_text:"Коршиносони байналмилалӣ дар бораи талабот ва роҳҳои содирот.",
            agenda1_5_title:"Қабули шом ва шабакасозӣ",
            agenda1_5_text:"Муоширати ғайрирасмӣ ва муаррифии шарикон.",

            speakers_title:"Суханронҳо",
            speakers_text:"Намояндагони ҳукумат, бизнес ва ниҳодҳои молиявӣ.",
            speaker1_name:"Мансабдори баландрутба",
            speaker1_role:"Сиёсати давлатӣ",
            speaker1_org:"Вазорати саноат ва технологияҳои нав",
            speaker2_name:"Роҳбари соҳаи энергетика",
            speaker2_role:"Энергетика ва ҳамгироӣ",
            speaker2_org:"Вазорати энергетика ва захираҳои об",
            speaker3_name:"Роҳбари ширкати кӯҳкорӣ",
            speaker3_role:"Идоракунии истеҳсолот",
            speaker3_org:"Ширкати байналмилалии кӯҳкорӣ",
            speaker4_name:"Директори сармоягузорӣ",
            speaker4_role:"Молия ва муомилот",
            speaker4_org:"Бонки рушди минтақавӣ",

            pricing_title:"Шартҳои иштирок",
            pricing_text:"Тарифҳои имтиёзнок то 15 феврали 2026 амал мекунанд.",
            price_virtual_title:"Иштироки онлайн",
            price_virtual_old:"$200",
            price_virtual_new:"$150",
            price_virtual_f1:"Пахши зиндаи ҳамаи ҷаласаҳо",
            price_virtual_f2:"Маводҳои электронии форум",
            price_virtual_f3:"Платформаи онлайнии муошират",
            price_virtual_f4:"Дастрасӣ ба сабтҳои ҷаласаҳо",
            price_full_title:"Иштироки ҳузурӣ",
            price_full_old:"$600",
            price_full_new:"$450",
            price_full_f1:"Ҳама ҷаласаҳо ва мастер-классҳо",
            price_full_f2:"Қаҳва-брейкҳо ва хӯроки нисфирӯзӣ",
            price_full_f3:"Чорабиниҳои шабакасозӣ",
            price_full_f4:"Шоми тантанавӣ",
            price_full_f5:"Саёҳат ба иншоот (ихтиёрӣ)",
            price_vip_title:"ВИП-делегатсия",
            price_vip_new:"$1,200",
            price_vip_f1:"Ҳама имтиёзҳои иштирокчии ҳузурӣ",
            price_vip_f2:"Ҷойҳои пеш дар толор",
            price_vip_f3:"Минтақаи ВИП ва хизматрасонии иловагӣ",
            price_vip_f4:"Ҷаласаҳои пӯшидаи мизҳои гирдӣ",
            price_vip_f5:"Интиқол аз фурудгоҳ",
            price_best:"Машҳуртарин",
            price_select:"Интихоб кардан",

            reg_title:"Бақайдгирӣ",
            reg_text:"Барои иштирок дар форум шакли зерро пур кунед.",
            reg_first_name:"Ном *",
            reg_last_name:"Насаб *",
            reg_email:"Email *",
            reg_phone:"Телефон *",
            reg_company:"Ширкат / ташкилот *",
            reg_job:"Вазифа *",
            reg_country:"Кишвар *",
            reg_sector:"Соҳаи фаъолият *",
            reg_package:"Навъи иштирок *",
            reg_days:"Рӯзҳои иштирок *",
            reg_day1:"Рӯз 1 – 26 март",
            reg_day2:"Рӯз 2 – 27 март",
            reg_day3:"Рӯз 3 – 28 март",
            reg_diet:"Маҳдудиятҳои ғизоӣ",
            reg_diet_none:"Маҳдудият нест",
            reg_diet_veg:"Ғизои вегетарианӣ",
            reg_diet_halal:"Ҳалол",
            reg_diet_other:"Дигар",
            reg_visa:"Оё ба дастгирии раводид ниёз доред?",
            reg_visa_no:"Не",
            reg_visa_yes:"Бале",
            reg_comments:"Дархостҳо ва шарҳҳои иловагӣ",
            reg_terms:"Ман бо Шартҳо ва Сиёсати махфияти форум розӣ ҳастам *",
            reg_newsletter:"Ман мехоҳам ахбори форумро тавассути email гирам",
            reg_submit:"Фиристодани дархост",

            sector_mining:"Истиҳроҷи маъдан ва кӯҳкорӣ",
            sector_energy:"Энергетика ва барқ",
            sector_equipment:"Таҷҳизот ва технология",
            sector_logistics:"Нақлиёт ва логистика",
            sector_government:"Мақомоти давлатӣ ва танзимкунӣ",
            sector_finance:"Молия ва сармоягузорӣ",
            sector_academia:"Илм ва маориф",
            sector_media:"Медиа ва матбуот",
            sector_other:"Дигар",

            reg_pkg_virtual:"Иштироки онлайн",
            reg_pkg_full:"Иштироки ҳузурӣ",
            reg_pkg_gov:"Мақомоти давлатӣ / академия",
            reg_pkg_vip:"ВИП-делегатсия",
            reg_pkg_booth:"Стенди намоишӣ",

            venue_title:"Ҷойи баргузорӣ",
            venue_name:"Hyatt Regency Dushanbe",
            venue_addr:"кӯчаи Исмоили Сомонӣ 26/1, шаҳри Душанбе, Тоҷикистон",
            venue_airport:"15 дақиқа аз фурудгоҳи байналмилалии Душанбе",
            venue_hotel:"Меҳмонхонаи 5-ситорадор бо тарифҳои махсус барои иштирокчиён",
            venue_food:"Хӯрок ва қаҳва-брейкҳои босифат",
            venue_wifi:"Wi‑Fi-и босуръат дар тамоми минтақа",

            partners_title:"Шарикон ва сарпарастон",
            partner1:"Шарики стратегӣ",
            partner2:"Сарпарасти тиллоӣ",
            partner3:"Сарпарасти нуқрагин",
            partner4:"Шарики соҳа",
            partner5:"Шарики иттилоотӣ",

            footer_about_title:"International Coal Forum",
            footer_about_text:"Майдони пешсаф барои соҳаи ангишт ва кӯҳкорӣ дар Осиёи Марказӣ.",
            footer_links_title:"Пайвандҳои зуд",
            footer_link_about:"Дар бораи форум",
            footer_link_agenda:"Барнома",
            footer_link_speakers:"Суханронҳо",
            footer_link_pricing:"Ширкат",
            footer_link_register:"Бақайдгирӣ",
            footer_contact_title:"Тамос",
            footer_org_title:"Ташкилкунанда",
            footer_org_text:"Вазорати саноат ва технологияҳои нави Ҷумҳурии Тоҷикистон.",
            footer_copy:"© 2026 Форуми байналмилалии ангишт Тоҷикистон. Ҳама ҳуқуқ ҳифз шудаанд."
        }
    };

    let currentLang = localStorage.getItem('ifc_lang') || 'en';

    function setLanguage(lang){
        currentLang = lang;
        const dict = translations[lang] || translations.en;
        document.documentElement.lang = lang;
        document.querySelectorAll('[data-i18n]').forEach(el=>{
            const key = el.getAttribute('data-i18n');
            if (dict[key]) el.textContent = dict[key];
        });
        document.querySelectorAll('.lang-btn').forEach(btn=>{
            btn.classList.toggle('active', btn.dataset.lang === lang);
        });
        localStorage.setItem('ifc_lang', lang);
    }

    document.querySelectorAll('.lang-btn').forEach(btn=>{
        btn.addEventListener('click', ()=> setLanguage(btn.dataset.lang));
    });

    setLanguage(currentLang);
