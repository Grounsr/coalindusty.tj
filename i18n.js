/* i18n.js — Trilingual Translation System (EN/RU/TJ) */

const translations = {
  en: {
    /* Navigation */
    'nav.home': 'Home',
    'nav.about': 'About',
    'nav.program': 'Program',
    'nav.speakers': 'Speakers',
    'nav.register': 'Register',
    'nav.media': 'Media',
    'nav.contacts': 'Contacts',

    /* Hero */
    'hero.badge': 'November 25, 2026 — Dushanbe',
    'hero.title': 'International Coal Industry Forum',
    'hero.subtitle': 'Tajikistan 2026',
    'hero.date': 'November 25, 2026 — Dushanbe, Tajikistan',
    'hero.tagline': 'Shaping the Future of Central Asian Coal: Growth, Innovation & Sustainable Development',
    'hero.cta': 'Register Now',
    'countdown.days': 'Days',
    'countdown.hours': 'Hours',
    'countdown.minutes': 'Minutes',
    'countdown.seconds': 'Seconds',

    /* Stats */
    'stats.participants': 'Participants',
    'stats.countries': 'Countries',
    'stats.speakers': 'Speakers',
    'stats.day': 'Day Event',

    /* About Brief (homepage) */
    'about.brief.title': 'About the Forum',
    'about.brief.text': 'The first-ever International Coal Industry Forum in Tajikistan brings together industry leaders, government officials, investors, and experts from across Central Asia and beyond. The forum is organized by the Ministry of Industry and New Technologies of the Republic of Tajikistan.',
    'about.brief.text2': 'The event aims to showcase Tajikistan\'s coal industry potential, foster international cooperation, and discuss modern approaches to coal mining, environmental sustainability, and investment opportunities.',
    'about.brief.btn': 'Learn More',

    /* About Page */
    'about.title': 'About the Forum',
    'about.subtitle': 'The first international coal industry event in Tajikistan',
    'about.concept.title': 'Forum Concept',
    'about.concept.p1': 'The International Coal Industry Forum Tajikistan 2026 is a landmark event dedicated to the development and modernization of the coal industry in the Republic of Tajikistan and Central Asia as a whole.',
    'about.concept.p2': 'For the first time, Tajikistan brings together leading experts, government officials, investors, mining companies, and international organizations to discuss the current state and future prospects of the coal industry in the region.',
    'about.concept.p3': 'The forum will serve as a platform for knowledge exchange, business networking, and strategic discussions that will shape the coal industry\'s trajectory for years to come.',
    'about.goals.title': 'Forum Goals',
    'about.goal1': 'Showcase the potential of Tajikistan\'s coal reserves and mining industry to the international community',
    'about.goal2': 'Facilitate dialogue between government, industry, and investors on coal sector development',
    'about.goal3': 'Present modern mining technologies and equipment from leading global manufacturers',
    'about.goal4': 'Discuss environmental sustainability and green technologies in coal mining',
    'about.goal5': 'Attract foreign investment to Tajikistan\'s mining sector',
    'about.goal6': 'Strengthen international cooperation in coal trade and logistics',
    'about.organizers.title': 'Organizers',
    'about.organizer.ministry': 'Ministry of Industry and New Technologies of the Republic of Tajikistan',
    'about.organizer.ministry.desc': 'The primary organizer of the forum, responsible for industrial policy and the development of Tajikistan\'s mining sector.',
    'about.coal.title': 'Coal Industry of Tajikistan',
    'about.coal.p1': 'Tajikistan possesses significant coal reserves estimated at over 4 billion tons, with major deposits located in the Fan-Yagnob, Nazarailok, Shurab, and Ziddi basins.',
    'about.coal.p2': 'The coal industry plays a vital role in the country\'s energy security, particularly during winter months when hydropower generation is limited. The government has identified coal sector development as a strategic priority.',
    'about.coal.p3': 'With growing energy demands and the need for diversified energy sources, Tajikistan\'s coal sector offers substantial opportunities for modernization, investment, and international partnership.',

    /* Topics */
    'topics.title': 'Key Topics',
    'topics.subtitle': 'Six major areas of discussion shaping the future of coal in Central Asia',
    'topic1.title': 'Current State and Prospects of Coal Industry in Central Asia',
    'topic1.desc': 'Analysis of the regional coal market, production trends, and strategic outlook for coal-producing nations in Central Asia.',
    'topic2.title': 'Modern Coal Mining Technologies and Equipment',
    'topic2.desc': 'Innovations in extraction methods, automation, digitalization, and advanced equipment from leading global manufacturers.',
    'topic3.title': 'Environmental Sustainability and Green Coal Technologies',
    'topic3.desc': 'Clean coal technologies, carbon capture, emission reduction strategies, and environmental rehabilitation of mining sites.',
    'topic4.title': 'Investment Opportunities in Tajikistan\'s Mining Sector',
    'topic4.desc': 'Exploration of investment incentives, regulatory framework, and major projects available for international investors.',
    'topic5.title': 'International Cooperation and Trade in Coal Markets',
    'topic5.desc': 'Cross-border trade dynamics, logistics infrastructure, export potential, and multilateral cooperation frameworks.',
    'topic6.title': 'Coal Industry Workforce Development and Safety Standards',
    'topic6.desc': 'Training programs, safety protocols, international best practices, and workforce modernization strategies.',

    /* Partners */
    'partners.title': 'Partners & Sponsors',
    'partners.subtitle': 'Organizations supporting the development of the coal industry',
    'partners.become': 'Become a Partner',
    'partner.govt': 'Government of the Republic of Tajikistan',
    'partner.energy': 'Central Asian Energy Association',
    'partner.mining': 'International Mining Council',
    'partner.invest': 'Tajikistan Investment Fund',
    'partner.tech': 'Mining Technologies International',
    'partner.eco': 'Environmental Mining Alliance',

    /* Program */
    'program.title': 'Forum Program',
    'program.subtitle': 'One-day program featuring plenary sessions, panels, and networking',
    'program.date': 'November 25, 2026',
    'prog.reg.time': '08:00–09:00',
    'prog.reg.title': 'Registration & Welcome Coffee',
    'prog.reg.desc': 'Arrival of delegates, badge collection, and networking over morning coffee in the networking area.',
    'prog.reg.tag': 'Registration',
    'prog.opening.time': '09:00–09:30',
    'prog.opening.title': 'Opening Ceremony',
    'prog.opening.desc': 'Welcome addresses by senior government officials and organizers. Official opening of the International Coal Industry Forum Tajikistan 2026.',
    'prog.opening.tag': 'Ceremony',
    'prog.plenary.time': '09:30–10:30',
    'prog.plenary.title': 'Plenary Session: Future of Coal in Central Asia',
    'prog.plenary.desc': 'Keynote presentations on the strategic role of coal in Central Asian energy policy, regional cooperation, and long-term development roadmap.',
    'prog.plenary.tag': 'Plenary',
    'prog.coffee1.time': '10:30–11:00',
    'prog.coffee1.title': 'Coffee Break',
    'prog.coffee1.desc': 'Refreshments and informal networking in the networking area.',
    'prog.coffee1.tag': 'Break',
    'prog.panel1.time': '11:00–12:30',
    'prog.panel1.title': 'Panel 1: Mining Technologies & Innovation',
    'prog.panel1.desc': 'Discussion of cutting-edge mining equipment, automation solutions, digital transformation, and technology transfer in the coal sector.',
    'prog.panel1.tag': 'Panel Discussion',
    'prog.lunch.time': '12:30–13:30',
    'prog.lunch.title': 'Networking Lunch',
    'prog.lunch.desc': 'Hosted lunch with dedicated networking tables for delegates and speakers.',
    'prog.lunch.tag': 'Networking',
    'prog.panel2.time': '13:30–15:00',
    'prog.panel2.title': 'Panel 2: Investment & Trade',
    'prog.panel2.desc': 'Presentations on investment climate, regulatory framework, international trade routes, and financing opportunities for coal sector projects.',
    'prog.panel2.tag': 'Panel Discussion',
    'prog.coffee2.time': '15:00–15:30',
    'prog.coffee2.title': 'Coffee Break',
    'prog.coffee2.desc': 'Refreshments and networking.',
    'prog.coffee2.tag': 'Break',
    'prog.panel3.time': '15:30–17:00',
    'prog.panel3.title': 'Panel 3: Sustainability & Green Technologies',
    'prog.panel3.desc': 'Expert discussion on environmental impact mitigation, clean coal technologies, carbon capture, and sustainable mining practices.',
    'prog.panel3.tag': 'Panel Discussion',
    'prog.closing.time': '17:00–17:30',
    'prog.closing.title': 'Closing Ceremony & Adoption of Resolution',
    'prog.closing.desc': 'Summary of key outcomes, adoption of the forum resolution, and concluding remarks by organizers and senior officials.',
    'prog.closing.tag': 'Ceremony',
    'prog.reception.time': '17:30–19:00',
    'prog.reception.title': 'Networking Reception',
    'prog.reception.desc': 'Evening reception with refreshments, and informal business meetings.',
    'prog.reception.tag': 'Networking',

    /* Speakers */
    'speakers.title': 'Speakers',
    'speakers.subtitle': 'Leading experts and officials from the coal and energy sectors',
    'speaker1.name': 'Rustam Karimov',
    'speaker1.position': 'Deputy Minister',
    'speaker1.org': 'Ministry of Industry and New Technologies of the Republic of Tajikistan',
    'speaker2.name': 'Dr. Elena Voronova',
    'speaker2.position': 'Director',
    'speaker2.org': 'Central Asian Energy Research Institute',
    'speaker3.name': 'Zhang Wei',
    'speaker3.position': 'CEO',
    'speaker3.org': 'Asia Pacific Mining Corporation',
    'speaker4.name': 'Murad Aliyev',
    'speaker4.position': 'Chief Engineer',
    'speaker4.org': 'Mining Technologies International',
    'speaker5.name': 'James Richardson',
    'speaker5.position': 'Investment Director',
    'speaker5.org': 'Global Mining Capital Partners',
    'speaker6.name': 'Bekzod Rakhimov',
    'speaker6.position': 'Trade Representative',
    'speaker6.org': 'Central Asian Trade & Logistics Association',
    'speaker7.name': 'Dr. Sarah Mitchell',
    'speaker7.position': 'Environmental Consultant',
    'speaker7.org': 'International Environmental Mining Group',
    'speaker8.name': 'Prof. Akbar Nazarov',
    'speaker8.position': 'Head of Research',
    'speaker8.org': 'Tajikistan Coal Research Institute',


    /* Register */
    'register.title': 'Registration',
    'register.subtitle': 'Attendance is free. Register to secure your place at the forum.',
    'register.note': 'Registration is required for all participants. You will receive a confirmation email with your delegate badge details.',
    'form.name': 'Full Name',
    'form.name.placeholder': 'Enter your full name',
    'form.org': 'Organization',
    'form.org.placeholder': 'Enter your organization name',
    'form.position': 'Position',
    'form.position.placeholder': 'Enter your position',
    'form.country': 'Country',
    'form.country.placeholder': 'Enter your country',
    'form.city': 'City',
    'form.city.placeholder': 'Enter your city',
    'form.email': 'Email',
    'form.email.placeholder': 'Enter your email address',
    'form.phone': 'Phone',
    'form.phone.placeholder': '+992 XX XXX-XXXX',
    'form.type': 'Participation Type',
    'form.type.delegate': 'Delegate',
    'form.type.speaker': 'Speaker',
    'form.type.media': 'Media',
    'form.submit': 'Register',
    'form.submitting': 'Submitting...',
    'form.required': 'This field is required',
    'form.email.invalid': 'Please enter a valid email address',
    'form.success.title': 'Registration Successful!',
    'form.success.text': 'Thank you for registering for the International Coal Industry Forum Tajikistan 2026. You will receive a confirmation email shortly.',
    'form.error.general': 'An error occurred. Please try again later.',

    /* Media */
    'media.title': 'Media Center',
    'media.subtitle': 'Photos, videos, and press materials from the forum',
    'media.photos': 'Photo Gallery',
    'media.videos': 'Videos',
    'media.press': 'Press Kit',
    'media.coming': 'Materials will be available after the forum',
    'media.accreditation': 'Media Accreditation',
    'media.accreditation.text': 'Representatives of media organizations are invited to apply for accreditation to cover the International Coal Industry Forum. Accredited journalists receive full access to all sessions, and press conferences.',
    'media.accreditation.btn': 'Request Accreditation',

    /* Contacts */
    'contacts.title': 'Contacts',
    'contacts.subtitle': 'Get in touch with the forum organizing committee',
    'contacts.general': 'General Inquiries',
    'contacts.registration': 'Registration',
    'contacts.phone': 'Phone',
    'contacts.address': 'Address',
    'contacts.address.value': 'Dushanbe, Tajikistan, Ismoili Somoni Avenue',
    'contacts.telegram': 'Telegram',
    'contacts.social': 'Social Media',
    'contacts.map.title': 'Venue Location',

    /* Footer */
    'footer.desc': 'The first international forum dedicated to the coal industry of Tajikistan and Central Asia.',
    'footer.links': 'Quick Links',
    'footer.contact': 'Contact Us',
    'footer.organizer': 'Organized by the Ministry of Industry and New Technologies of the Republic of Tajikistan',
    'footer.rights': '© 2026 International Coal Industry Forum Tajikistan. All rights reserved.',
  },

  ru: {
    /* Навигация */
    'nav.home': 'Главная',
    'nav.about': 'О форуме',
    'nav.program': 'Программа',
    'nav.speakers': 'Спикеры',
    'nav.register': 'Регистрация',
    'nav.media': 'Медиа',
    'nav.contacts': 'Контакты',

    /* Герой */
    'hero.badge': '25 ноября 2026 — Душанбе',
    'hero.title': 'Международный форум угольной промышленности',
    'hero.subtitle': 'Таджикистан 2026',
    'hero.date': '25 ноября 2026 — Душанбе, Таджикистан',
    'hero.tagline': 'Формируя будущее угольной промышленности Центральной Азии: рост, инновации и устойчивое развитие',
    'hero.cta': 'Зарегистрироваться',
    'countdown.days': 'Дней',
    'countdown.hours': 'Часов',
    'countdown.minutes': 'Минут',
    'countdown.seconds': 'Секунд',

    /* Статистика */
    'stats.participants': 'Участников',
    'stats.countries': 'Стран',
    'stats.speakers': 'Спикеров',
    'stats.day': 'День форума',

    /* О форуме (главная) */
    'about.brief.title': 'О форуме',
    'about.brief.text': 'Первый в истории Международный форум угольной промышленности в Таджикистане объединяет лидеров отрасли, государственных чиновников, инвесторов и экспертов из Центральной Азии и за её пределами. Форум организован Министерством промышленности и новых технологий Республики Таджикистан.',
    'about.brief.text2': 'Мероприятие направлено на демонстрацию потенциала угольной промышленности Таджикистана, развитие международного сотрудничества и обсуждение современных подходов к добыче угля, экологической устойчивости и инвестиционных возможностей.',
    'about.brief.btn': 'Подробнее',

    /* Страница о форуме */
    'about.title': 'О форуме',
    'about.subtitle': 'Первое международное событие угольной промышленности в Таджикистане',
    'about.concept.title': 'Концепция форума',
    'about.concept.p1': 'Международный форум угольной промышленности Таджикистан 2026 — это знаковое мероприятие, посвящённое развитию и модернизации угольной промышленности Республики Таджикистан и Центральной Азии в целом.',
    'about.concept.p2': 'Впервые Таджикистан собирает вместе ведущих экспертов, государственных деятелей, инвесторов, горнодобывающие компании и международные организации для обсуждения текущего состояния и перспектив развития угольной промышленности региона.',
    'about.concept.p3': 'Форум станет платформой для обмена знаниями, делового общения и стратегических дискуссий, которые определят траекторию развития угольной промышленности на годы вперёд.',
    'about.goals.title': 'Цели форума',
    'about.goal1': 'Продемонстрировать потенциал угольных месторождений и горнодобывающей промышленности Таджикистана международному сообществу',
    'about.goal2': 'Содействовать диалогу между государством, промышленностью и инвесторами по развитию угольного сектора',
    'about.goal3': 'Представить современные горнодобывающие технологии и оборудование ведущих мировых производителей',
    'about.goal4': 'Обсудить экологическую устойчивость и зелёные технологии в угольной добыче',
    'about.goal5': 'Привлечь иностранные инвестиции в горнодобывающий сектор Таджикистана',
    'about.goal6': 'Укрепить международное сотрудничество в торговле углём и логистике',
    'about.organizers.title': 'Организаторы',
    'about.organizer.ministry': 'Министерство промышленности и новых технологий Республики Таджикистан',
    'about.organizer.ministry.desc': 'Главный организатор форума, ответственный за промышленную политику и развитие горнодобывающего сектора Таджикистана.',
    'about.coal.title': 'Угольная промышленность Таджикистана',
    'about.coal.p1': 'Таджикистан обладает значительными запасами угля, оценёнными более чем в 4 миллиарда тонн, с основными месторождениями в бассейнах Фан-Ягноб, Назарайлок, Шураб и Зидди.',
    'about.coal.p2': 'Угольная промышленность играет жизненно важную роль в энергетической безопасности страны, особенно в зимние месяцы, когда выработка гидроэнергии ограничена. Правительство определило развитие угольного сектора как стратегический приоритет.',
    'about.coal.p3': 'С учётом растущих потребностей в энергии и необходимости диверсификации энергетических источников, угольный сектор Таджикистана предлагает значительные возможности для модернизации, инвестиций и международного партнёрства.',

    /* Темы */
    'topics.title': 'Ключевые темы',
    'topics.subtitle': 'Шесть основных направлений дискуссии о будущем угля в Центральной Азии',
    'topic1.title': 'Текущее состояние и перспективы угольной промышленности Центральной Азии',
    'topic1.desc': 'Анализ регионального угольного рынка, тенденции производства и стратегические перспективы угледобывающих стран Центральной Азии.',
    'topic2.title': 'Современные технологии и оборудование для добычи угля',
    'topic2.desc': 'Инновации в методах добычи, автоматизация, цифровизация и передовое оборудование от ведущих мировых производителей.',
    'topic3.title': 'Экологическая устойчивость и зелёные угольные технологии',
    'topic3.desc': 'Чистые угольные технологии, улавливание углерода, стратегии снижения выбросов и экологическая реабилитация горных участков.',
    'topic4.title': 'Инвестиционные возможности в горнодобывающем секторе Таджикистана',
    'topic4.desc': 'Обзор инвестиционных стимулов, нормативной базы и крупных проектов, доступных для международных инвесторов.',
    'topic5.title': 'Международное сотрудничество и торговля на угольных рынках',
    'topic5.desc': 'Динамика трансграничной торговли, логистическая инфраструктура, экспортный потенциал и рамки многостороннего сотрудничества.',
    'topic6.title': 'Развитие кадрового потенциала и стандарты безопасности',
    'topic6.desc': 'Программы обучения, протоколы безопасности, лучшие международные практики и стратегии модернизации рабочей силы.',

    /* Партнёры */
    'partners.title': 'Партнёры и спонсоры',
    'partners.subtitle': 'Организации, поддерживающие развитие угольной промышленности',
    'partners.become': 'Стать партнёром',
    'partner.govt': 'Правительство Республики Таджикистан',
    'partner.energy': 'Энергетическая ассоциация Центральной Азии',
    'partner.mining': 'Международный горнодобывающий совет',
    'partner.invest': 'Инвестиционный фонд Таджикистана',
    'partner.tech': 'Mining Technologies International',
    'partner.eco': 'Экологический горнодобывающий альянс',

    /* Программа */
    'program.title': 'Программа форума',
    'program.subtitle': 'Однодневная программа с пленарными заседаниями, панельными дискуссиями и нетворкингом',
    'program.date': '25 ноября 2026',
    'prog.reg.time': '08:00–09:00',
    'prog.reg.title': 'Регистрация и приветственный кофе',
    'prog.reg.desc': 'Прибытие делегатов, получение бейджей и общение за утренним кофе в зоне нетворкинга.',
    'prog.reg.tag': 'Регистрация',
    'prog.opening.time': '09:00–09:30',
    'prog.opening.title': 'Торжественное открытие',
    'prog.opening.desc': 'Приветственные обращения высокопоставленных государственных чиновников и организаторов. Официальное открытие Международного форума угольной промышленности Таджикистан 2026.',
    'prog.opening.tag': 'Церемония',
    'prog.plenary.time': '09:30–10:30',
    'prog.plenary.title': 'Пленарное заседание: Будущее угля в Центральной Азии',
    'prog.plenary.desc': 'Ключевые доклады о стратегической роли угля в энергетической политике Центральной Азии, региональном сотрудничестве и долгосрочной дорожной карте развития.',
    'prog.plenary.tag': 'Пленарное заседание',
    'prog.coffee1.time': '10:30–11:00',
    'prog.coffee1.title': 'Кофе-пауза',
    'prog.coffee1.desc': 'Прохладительные напитки и неформальное общение в зоне нетворкинга.',
    'prog.coffee1.tag': 'Перерыв',
    'prog.panel1.time': '11:00–12:30',
    'prog.panel1.title': 'Панель 1: Горнодобывающие технологии и инновации',
    'prog.panel1.desc': 'Обсуждение передового горнодобывающего оборудования, решений по автоматизации, цифровой трансформации и трансфера технологий в угольном секторе.',
    'prog.panel1.tag': 'Панельная дискуссия',
    'prog.lunch.time': '12:30–13:30',
    'prog.lunch.title': 'Деловой обед',
    'prog.lunch.desc': 'Обед с отведёнными столами для нетворкинга делегатов, спикеров и экспертов.',
    'prog.lunch.tag': 'Нетворкинг',
    'prog.panel2.time': '13:30–15:00',
    'prog.panel2.title': 'Панель 2: Инвестиции и торговля',
    'prog.panel2.desc': 'Презентации инвестиционного климата, нормативной базы, международных торговых маршрутов и возможностей финансирования проектов в угольном секторе.',
    'prog.panel2.tag': 'Панельная дискуссия',
    'prog.coffee2.time': '15:00–15:30',
    'prog.coffee2.title': 'Кофе-пауза',
    'prog.coffee2.desc': 'Прохладительные напитки и неформальное общение.',
    'prog.coffee2.tag': 'Перерыв',
    'prog.panel3.time': '15:30–17:00',
    'prog.panel3.title': 'Панель 3: Устойчивое развитие и зелёные технологии',
    'prog.panel3.desc': 'Экспертное обсуждение смягчения воздействия на окружающую среду, чистых угольных технологий, улавливания углерода и устойчивых горнодобывающих практик.',
    'prog.panel3.tag': 'Панельная дискуссия',
    'prog.closing.time': '17:00–17:30',
    'prog.closing.title': 'Торжественное закрытие и принятие резолюции',
    'prog.closing.desc': 'Подведение итогов, принятие резолюции форума и заключительные выступления организаторов и высокопоставленных лиц.',
    'prog.closing.tag': 'Церемония',
    'prog.reception.time': '17:30–19:00',
    'prog.reception.title': 'Торжественный приём',
    'prog.reception.desc': 'Вечерний приём с угощениями, неформальными деловыми встречами.',
    'prog.reception.tag': 'Нетворкинг',

    /* Спикеры */
    'speakers.title': 'Спикеры',
    'speakers.subtitle': 'Ведущие эксперты и руководители угольной и энергетической отраслей',
    'speaker1.name': 'Рустам Каримов',
    'speaker1.position': 'Заместитель министра',
    'speaker1.org': 'Министерство промышленности и новых технологий Республики Таджикистан',
    'speaker2.name': 'Д-р Елена Воронова',
    'speaker2.position': 'Директор',
    'speaker2.org': 'Центральноазиатский институт энергетических исследований',
    'speaker3.name': 'Чжан Вэй',
    'speaker3.position': 'Генеральный директор',
    'speaker3.org': 'Asia Pacific Mining Corporation',
    'speaker4.name': 'Мурад Алиев',
    'speaker4.position': 'Главный инженер',
    'speaker4.org': 'Mining Technologies International',
    'speaker5.name': 'Джеймс Ричардсон',
    'speaker5.position': 'Инвестиционный директор',
    'speaker5.org': 'Global Mining Capital Partners',
    'speaker6.name': 'Бекзод Рахимов',
    'speaker6.position': 'Торговый представитель',
    'speaker6.org': 'Ассоциация торговли и логистики Центральной Азии',
    'speaker7.name': 'Д-р Сара Митчелл',
    'speaker7.position': 'Экологический консультант',
    'speaker7.org': 'Международная экологическая группа горнодобычи',
    'speaker8.name': 'Проф. Акбар Назаров',
    'speaker8.position': 'Глава исследовательского отдела',
    'speaker8.org': 'Научно-исследовательский институт угля Таджикистана',

    /* Выставка */

    /* Регистрация */
    'register.title': 'Регистрация',
    'register.subtitle': 'Участие бесплатное. Зарегистрируйтесь, чтобы обеспечить своё место на форуме.',
    'register.note': 'Регистрация обязательна для всех участников. Вы получите подтверждение по электронной почте с деталями вашего бейджа.',
    'form.name': 'Полное имя',
    'form.name.placeholder': 'Введите ваше полное имя',
    'form.org': 'Организация',
    'form.org.placeholder': 'Введите название организации',
    'form.position': 'Должность',
    'form.position.placeholder': 'Введите вашу должность',
    'form.country': 'Страна',
    'form.country.placeholder': 'Введите вашу страну',
    'form.city': 'Город',
    'form.city.placeholder': 'Введите ваш город',
    'form.email': 'Электронная почта',
    'form.email.placeholder': 'Введите адрес электронной почты',
    'form.phone': 'Телефон',
    'form.phone.placeholder': '+992 XX XXX-XXXX',
    'form.type': 'Тип участия',
    'form.type.delegate': 'Делегат',
    'form.type.speaker': 'Спикер',
    'form.type.media': 'Пресса',
    'form.submit': 'Зарегистрироваться',
    'form.submitting': 'Отправка...',
    'form.required': 'Это поле обязательно',
    'form.email.invalid': 'Пожалуйста, введите корректный адрес электронной почты',
    'form.success.title': 'Регистрация успешна!',
    'form.success.text': 'Благодарим вас за регистрацию на Международный форум угольной промышленности Таджикистан 2026. Вы получите подтверждение по электронной почте в ближайшее время.',
    'form.error.general': 'Произошла ошибка. Пожалуйста, попробуйте позже.',

    /* Медиа */
    'media.title': 'Медиа-центр',
    'media.subtitle': 'Фото, видео и пресс-материалы форума',
    'media.photos': 'Фотогалерея',
    'media.videos': 'Видео',
    'media.press': 'Пресс-кит',
    'media.coming': 'Материалы будут доступны после проведения форума',
    'media.accreditation': 'Аккредитация СМИ',
    'media.accreditation.text': 'Представителям медиа-организаций предлагается подать заявку на аккредитацию для освещения Международного форума угольной промышленности. Аккредитованные журналисты получают полный доступ ко всем сессиям, выставочной зоне и пресс-конференциям.',
    'media.accreditation.btn': 'Запросить аккредитацию',

    /* Контакты */
    'contacts.title': 'Контакты',
    'contacts.subtitle': 'Свяжитесь с организационным комитетом форума',
    'contacts.general': 'Общие вопросы',
    'contacts.registration': 'Регистрация',
    'contacts.phone': 'Телефон',
    'contacts.address': 'Адрес',
    'contacts.address.value': 'г. Душанбе, Таджикистан, пр. Исмоили Сомони',
    'contacts.telegram': 'Телеграм',
    'contacts.social': 'Социальные сети',
    'contacts.map.title': 'Местоположение площадки',

    /* Подвал */
    'footer.desc': 'Первый международный форум, посвящённый угольной промышленности Таджикистана и Центральной Азии.',
    'footer.links': 'Быстрые ссылки',
    'footer.contact': 'Свяжитесь с нами',
    'footer.organizer': 'Организован Министерством промышленности и новых технологий Республики Таджикистан',
    'footer.rights': '© 2026 Международный форум угольной промышленности Таджикистан. Все права защищены.',
  },

  tj: {
    /* Навигатсия */
    'nav.home': 'Саҳифаи асосӣ',
    'nav.about': 'Дар бораи форум',
    'nav.program': 'Барнома',
    'nav.speakers': 'Суханронҳо',
    'nav.register': 'Бақайдгирӣ',
    'nav.media': 'Медиа',
    'nav.contacts': 'Тамос',

    /* Қаҳрамон */
    'hero.badge': '25 ноябри 2026 — Душанбе',
    'hero.title': 'Форуми байналмилалии саноати ангишт',
    'hero.subtitle': 'Тоҷикистон 2026',
    'hero.date': '25 ноябри 2026 — Душанбе, Тоҷикистон',
    'hero.tagline': 'Ояндасозии саноати ангишти Осиёи Марказӣ: рушд, навоварӣ ва рушди устувор',
    'hero.cta': 'Бақайдгирӣ',
    'countdown.days': 'Рӯз',
    'countdown.hours': 'Соат',
    'countdown.minutes': 'Дақиқа',
    'countdown.seconds': 'Сония',

    /* Омор */
    'stats.participants': 'Иштирокчиён',
    'stats.countries': 'Кишварҳо',
    'stats.speakers': 'Суханронҳо',
    'stats.day': 'Рӯз',

    /* Дар бораи форум (саҳифаи асосӣ) */
    'about.brief.title': 'Дар бораи форум',
    'about.brief.text': 'Аввалин Форуми байналмилалии саноати ангишт дар Тоҷикистон роҳбарони соҳа, мақомоти давлатӣ, сармоягузорон ва мутахассисонро аз саросари Осиёи Марказӣ ва берун аз он муттаҳид мекунад. Форум аз ҷониби Вазорати саноат ва технологияҳои нави Ҷумҳурии Тоҷикистон ташкил шудааст.',
    'about.brief.text2': 'Ин чорабинӣ ба намоиш додани имконоти саноати ангишти Тоҷикистон, рушди ҳамкориҳои байналмилалӣ ва муҳокимаи равишҳои муосир ба истихроҷи ангишт, устувории экологӣ ва имконоти сармоягузорӣ равона шудааст.',
    'about.brief.btn': 'Бештар хонед',

    /* Саҳифаи дар бораи форум */
    'about.title': 'Дар бораи форум',
    'about.subtitle': 'Аввалин чорабинии байналмилалии саноати ангишт дар Тоҷикистон',
    'about.concept.title': 'Консепсияи форум',
    'about.concept.p1': 'Форуми байналмилалии саноати ангишти Тоҷикистон 2026 як чорабинии таърихист, ки ба рушд ва модернизатсияи саноати ангишти Ҷумҳурии Тоҷикистон ва Осиёи Марказӣ дар маҷмӯъ бахшида шудааст.',
    'about.concept.p2': 'Бори аввал Тоҷикистон мутахассисони пешбар, давлатмардон, сармоягузорон, ширкатҳои конӣ ва ташкилотҳои байналмилалиро барои муҳокимаи вазъи ҷорӣ ва дурнамои рушди саноати ангишт дар минтақа муттаҳид мекунад.',
    'about.concept.p3': 'Форум ҳамчун платформа барои мубодилаи дониш, робитаи тиҷоратӣ ва муҳокимаҳои стратегӣ хизмат хоҳад кард.',
    'about.goals.title': 'Ҳадафҳои форум',
    'about.goal1': 'Намоиш додани имконоти захираҳои ангишт ва саноати конии Тоҷикистон ба ҷомеаи байналмилалӣ',
    'about.goal2': 'Мусоидат ба муколама байни давлат, саноат ва сармоягузорон дар бораи рушди соҳаи ангишт',
    'about.goal3': 'Пешниҳоди технологияҳо ва таҷҳизоти муосири конӣ аз истеҳсолкунандагони пешбари ҷаҳон',
    'about.goal4': 'Муҳокимаи устувории экологӣ ва технологияҳои сабз дар истихроҷи ангишт',
    'about.goal5': 'Ҷалби сармоягузории хориҷӣ ба соҳаи конии Тоҷикистон',
    'about.goal6': 'Мустаҳкам кардани ҳамкории байналмилалӣ дар тиҷорат ва логистикаи ангишт',
    'about.organizers.title': 'Ташкилотчиён',
    'about.organizer.ministry': 'Вазорати саноат ва технологияҳои нави Ҷумҳурии Тоҷикистон',
    'about.organizer.ministry.desc': 'Ташкилотчии асосии форум, ки барои сиёсати саноатӣ ва рушди соҳаи конии Тоҷикистон масъул аст.',
    'about.coal.title': 'Саноати ангишти Тоҷикистон',
    'about.coal.p1': 'Тоҷикистон дорои захираҳои калони ангишт аст, ки зиёда аз 4 миллиард тонна арзёбӣ шудааст, бо конҳои асосӣ дар ҳавзаҳои Фан-Ягноб, Назарайлок, Шӯроб ва Зиддӣ.',
    'about.coal.p2': 'Саноати ангишт дар амнияти энергетикии кишвар нақши муҳим мебозад, алахусус дар моҳҳои зимистон, вақте ки истеҳсоли гидроэнергия маҳдуд аст. Ҳукумат рушди соҳаи ангиштро ҳамчун афзалияти стратегӣ муайян кардааст.',
    'about.coal.p3': 'Бо талаботи афзояндаи энергия ва зарурати гуногунсозии манбаъҳои энергия, соҳаи ангишти Тоҷикистон имконоти калон барои модернизатсия, сармоягузорӣ ва шарикии байналмилалӣ пешниҳод мекунад.',

    /* Мавзӯъҳо */
    'topics.title': 'Мавзӯъҳои асосӣ',
    'topics.subtitle': 'Шаш самти асосии баҳс дар бораи ояндаи ангишт дар Осиёи Марказӣ',
    'topic1.title': 'Вазъи ҷорӣ ва дурнамои саноати ангишти Осиёи Марказӣ',
    'topic1.desc': 'Таҳлили бозори ангишти минтақавӣ, тамоюлҳои истеҳсолот ва дурнамои стратегии кишварҳои ангиштистихроҷкунандаи Осиёи Марказӣ.',
    'topic2.title': 'Технологияҳо ва таҷҳизоти муосири истихроҷи ангишт',
    'topic2.desc': 'Навоварӣ дар усулҳои истихроҷ, автоматикунонӣ, рақамисозӣ ва таҷҳизоти пешрафта аз истеҳсолкунандагони пешбари ҷаҳон.',
    'topic3.title': 'Устувории экологӣ ва технологияҳои сабзи ангишт',
    'topic3.desc': 'Технологияҳои тозаи ангишт, забти карбон, стратегияҳои коҳиши партовҳо ва барқарорсозии экологии маконҳои конӣ.',
    'topic4.title': 'Имконоти сармоягузорӣ дар соҳаи конии Тоҷикистон',
    'topic4.desc': 'Баррасии ангезаҳои сармоягузорӣ, чаҳорчӯбаи меъёрӣ ва лоиҳаҳои калон барои сармоягузорони байналмилалӣ.',
    'topic5.title': 'Ҳамкории байналмилалӣ ва тиҷорат дар бозорҳои ангишт',
    'topic5.desc': 'Динамикаи тиҷорати байнисарҳадӣ, инфрасохтори логистикӣ, имконоти содирот ва чаҳорчӯбаҳои ҳамкории бисёрҷониба.',
    'topic6.title': 'Рушди кадрҳои соҳаи ангишт ва стандартҳои бехатарӣ',
    'topic6.desc': 'Барномаҳои омӯзиш, протоколҳои бехатарӣ, беҳтарин таҷрибаҳои байналмилалӣ ва стратегияҳои модернизатсияи қувваи коргарӣ.',

    /* Шарикон */
    'partners.title': 'Шарикон ва спонсорон',
    'partners.subtitle': 'Ташкилотҳое, ки рушди саноати ангиштро дастгирӣ мекунанд',
    'partners.become': 'Шарик шудан',
    'partner.govt': 'Ҳукумати Ҷумҳурии Тоҷикистон',
    'partner.energy': 'Иттиҳоди энергетикии Осиёи Марказӣ',
    'partner.mining': 'Шӯрои байналмилалии конӣ',
    'partner.invest': 'Фонди сармоягузории Тоҷикистон',
    'partner.tech': 'Mining Technologies International',
    'partner.eco': 'Иттиҳоди экологии конӣ',

    /* Барнома */
    'program.title': 'Барномаи форум',
    'program.subtitle': 'Барномаи якрӯза бо ҷаласаҳои пленарӣ, баҳсҳои панелӣ ва шабакасозӣ',
    'program.date': '25 ноябри 2026',
    'prog.reg.time': '08:00–09:00',
    'prog.reg.title': 'Бақайдгирӣ ва қаҳваи хушомадгӯӣ',
    'prog.reg.desc': 'Омадани ҳайати намояндагон, гирифтани нишонаҳо ва муоширати субҳонагӣ.',
    'prog.reg.tag': 'Бақайдгирӣ',
    'prog.opening.time': '09:00–09:30',
    'prog.opening.title': 'Маросими кушоиш',
    'prog.opening.desc': 'Суханрониҳои хушомадгӯии мақомоти олии давлатӣ ва ташкилотчиён. Кушоиши расмии Форуми байналмилалии саноати ангишти Тоҷикистон 2026.',
    'prog.opening.tag': 'Маросим',
    'prog.plenary.time': '09:30–10:30',
    'prog.plenary.title': 'Ҷаласаи пленарӣ: Ояндаи ангишт дар Осиёи Марказӣ',
    'prog.plenary.desc': 'Маърӯзаҳои асосӣ дар бораи нақши стратегии ангишт дар сиёсати энергетикии Осиёи Марказӣ, ҳамкории минтақавӣ ва нақшаи дарозмуддат.',
    'prog.plenary.tag': 'Ҷаласаи пленарӣ',
    'prog.coffee1.time': '10:30–11:00',
    'prog.coffee1.title': 'Танаффус',
    'prog.coffee1.desc': 'Нӯшиданиҳо ва муоширати ғайрирасмӣ.',
    'prog.coffee1.tag': 'Танаффус',
    'prog.panel1.time': '11:00–12:30',
    'prog.panel1.title': 'Панели 1: Технологияҳо ва навоварии конӣ',
    'prog.panel1.desc': 'Муҳокимаи таҷҳизоти пешрафтаи конӣ, ҳалҳои автоматикунонӣ, тағйироти рақамӣ ва интиқоли технология дар соҳаи ангишт.',
    'prog.panel1.tag': 'Баҳси панелӣ',
    'prog.lunch.time': '12:30–13:30',
    'prog.lunch.title': 'Хӯроки тиҷоратӣ',
    'prog.lunch.desc': 'Хӯрок бо мизҳои махсус барои шабакасозии ҳайати намояндагон, суханронҳо ва иштирокчиён.',
    'prog.lunch.tag': 'Шабакасозӣ',
    'prog.panel2.time': '13:30–15:00',
    'prog.panel2.title': 'Панели 2: Сармоягузорӣ ва тиҷорат',
    'prog.panel2.desc': 'Презентатсияҳо дар бораи фазои сармоягузорӣ, чаҳорчӯбаи меъёрӣ, масирҳои тиҷорати байналмилалӣ ва имконоти маблағгузории лоиҳаҳо.',
    'prog.panel2.tag': 'Баҳси панелӣ',
    'prog.coffee2.time': '15:00–15:30',
    'prog.coffee2.title': 'Танаффус',
    'prog.coffee2.desc': 'Нӯшиданиҳо ва шабакасозӣ.',
    'prog.coffee2.tag': 'Танаффус',
    'prog.panel3.time': '15:30–17:00',
    'prog.panel3.title': 'Панели 3: Устуворӣ ва технологияҳои сабз',
    'prog.panel3.desc': 'Муҳокимаи мутахассисонаи коҳиши таъсири экологӣ, технологияҳои тозаи ангишт, забти карбон ва таҷрибаҳои устувори конӣ.',
    'prog.panel3.tag': 'Баҳси панелӣ',
    'prog.closing.time': '17:00–17:30',
    'prog.closing.title': 'Маросими хотима ва қабули қатънома',
    'prog.closing.desc': 'Ҷамъбасти натиҷаҳо, қабули қатъномаи форум ва суханрониҳои хотимавии ташкилотчиён ва мақомоти олӣ.',
    'prog.closing.tag': 'Маросим',
    'prog.reception.time': '17:30–19:00',
    'prog.reception.title': 'Зиёфати шабакасозӣ',
    'prog.reception.desc': 'Зиёфати бегоҳӣ бо пазироӣ ва мулоқотҳои ғайрирасмии тиҷоратӣ.',
    'prog.reception.tag': 'Шабакасозӣ',

    /* Суханронҳо */
    'speakers.title': 'Суханронҳо',
    'speakers.subtitle': 'Мутахассисон ва роҳбарони пешбари соҳаи ангишт ва энергетика',
    'speaker1.name': 'Рустам Каримов',
    'speaker1.position': 'Муовини вазир',
    'speaker1.org': 'Вазорати саноат ва технологияҳои нави Ҷумҳурии Тоҷикистон',
    'speaker2.name': 'Д-р Елена Воронова',
    'speaker2.position': 'Директор',
    'speaker2.org': 'Институти тадқиқоти энергетикии Осиёи Марказӣ',
    'speaker3.name': 'Чжан Вэй',
    'speaker3.position': 'Директори генералӣ',
    'speaker3.org': 'Asia Pacific Mining Corporation',
    'speaker4.name': 'Мурод Алиев',
    'speaker4.position': 'Муҳандиси калон',
    'speaker4.org': 'Mining Technologies International',
    'speaker5.name': 'Ҷеймс Ричардсон',
    'speaker5.position': 'Директори сармоягузорӣ',
    'speaker5.org': 'Global Mining Capital Partners',
    'speaker6.name': 'Бекзод Раҳимов',
    'speaker6.position': 'Намояндаи тиҷоратӣ',
    'speaker6.org': 'Ассотсиатсияи тиҷорат ва логистикаи Осиёи Марказӣ',
    'speaker7.name': 'Д-р Сара Митчелл',
    'speaker7.position': 'Маслиҳатчии экологӣ',
    'speaker7.org': 'Гурӯҳи байналмилалии экологии конӣ',
    'speaker8.name': 'Проф. Акбар Назаров',
    'speaker8.position': 'Сарвари тадқиқот',
    'speaker8.org': 'Институти тадқиқоти ангишти Тоҷикистон',

    /* Намоишгоҳ */

    /* Бақайдгирӣ */
    'register.title': 'Бақайдгирӣ',
    'register.subtitle': 'Иштирок ройгон аст. Барои таъмини ҷои худ дар форум бақайд гиред.',
    'register.note': 'Бақайдгирӣ барои ҳамаи иштирокчиён ҳатмист. Шумо тасдиқро тавассути почтаи электронӣ мегиред.',
    'form.name': 'Номи пурра',
    'form.name.placeholder': 'Номи пурраи худро ворид кунед',
    'form.org': 'Ташкилот',
    'form.org.placeholder': 'Номи ташкилоти худро ворид кунед',
    'form.position': 'Вазифа',
    'form.position.placeholder': 'Вазифаи худро ворид кунед',
    'form.country': 'Кишвар',
    'form.country.placeholder': 'Кишвари худро ворид кунед',
    'form.city': 'Шаҳр',
    'form.city.placeholder': 'Шаҳри худро ворид кунед',
    'form.email': 'Почтаи электронӣ',
    'form.email.placeholder': 'Суроғаи почтаи электрониро ворид кунед',
    'form.phone': 'Телефон',
    'form.phone.placeholder': '+992 XX XXX-XXXX',
    'form.type': 'Навъи иштирок',
    'form.type.delegate': 'Ҳайати намояндагон',
    'form.type.speaker': 'Суханрон',
    'form.type.media': 'Расонаҳо',
    'form.submit': 'Бақайдгирӣ',
    'form.submitting': 'Фиристодан...',
    'form.required': 'Ин майдон ҳатмист',
    'form.email.invalid': 'Лутфан суроғаи дурусти почтаи электрониро ворид кунед',
    'form.success.title': 'Бақайдгирӣ бомуваффақият анҷом ёфт!',
    'form.success.text': 'Ташаккур барои бақайдгирӣ дар Форуми байналмилалии саноати ангишти Тоҷикистон 2026. Шумо ба наздикӣ тасдиқро тавассути почтаи электронӣ мегиред.',
    'form.error.general': 'Хатогӣ рух дод. Лутфан баъдтар кӯшиш кунед.',

    /* Медиа */
    'media.title': 'Маркази медиа',
    'media.subtitle': 'Акс, видео ва маводи матбуотии форум',
    'media.photos': 'Галереяи акс',
    'media.videos': 'Видеоҳо',
    'media.press': 'Маҷмӯи матбуот',
    'media.coming': 'Маводҳо пас аз гузаронидани форум дастрас мешаванд',
    'media.accreditation': 'Аккредитатсияи расонаҳо',
    'media.accreditation.text': 'Намояндагони ташкилотҳои расонаӣ даъват карда мешаванд, ки барои пӯшиши Форуми байналмилалии саноати ангишт дархости аккредитатсия диҳанд. Журналистони аккредитатсияшуда ба ҳамаи ҷаласаҳо, конфронсҳои матбуотӣ дастрасии пурра мегиранд.',
    'media.accreditation.btn': 'Дархости аккредитатсия',

    /* Тамос */
    'contacts.title': 'Тамос',
    'contacts.subtitle': 'Бо кумитаи ташкилии форум тамос гиред',
    'contacts.general': 'Саволҳои умумӣ',
    'contacts.registration': 'Бақайдгирӣ',
    'contacts.phone': 'Телефон',
    'contacts.address': 'Суроға',
    'contacts.address.value': 'ш. Душанбе, Тоҷикистон, хиёбони Исмоили Сомонӣ',
    'contacts.telegram': 'Телеграм',
    'contacts.social': 'Шабакаҳои иҷтимоӣ',
    'contacts.map.title': 'Ҷойгиршавии маҳалли гузаронидан',

    /* Поёнгаҳ */
    'footer.desc': 'Аввалин форуми байналмилалӣ, ки ба саноати ангишти Тоҷикистон ва Осиёи Марказӣ бахшида шудааст.',
    'footer.links': 'Истинодҳои зуд',
    'footer.contact': 'Бо мо тамос гиред',
    'footer.organizer': 'Аз ҷониби Вазорати саноат ва технологияҳои нави Ҷумҳурии Тоҷикистон ташкил шудааст',
    'footer.rights': '© 2026 Форуми байналмилалии саноати ангишти Тоҷикистон. Ҳамаи ҳуқуқҳо ҳифз шудаанд.',
  }
};

/* ============================================
   i18n Engine
   ============================================ */
function getDefaultLang() {
  const urlParams = new URLSearchParams(window.location.search);
  const urlLang = urlParams.get('lang');
  if (urlLang && translations[urlLang]) return urlLang;
  
  // In-memory only — browser storage not available in sandboxed iframes
  
  return 'ru'; // Default language is Russian
}

let currentLang = getDefaultLang();

function setLanguage(lang) {
  if (!translations[lang]) return;
  currentLang = lang;
  
  // Language stored in-memory via currentLang variable

  // Update text content
  document.querySelectorAll('[data-i18n]').forEach(el => {
    const key = el.getAttribute('data-i18n');
    if (translations[lang][key]) {
      el.textContent = translations[lang][key];
    }
  });

  // Update placeholders
  document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
    const key = el.getAttribute('data-i18n-placeholder');
    if (translations[lang][key]) {
      el.placeholder = translations[lang][key];
    }
  });

  // Update active lang button
  document.querySelectorAll('.lang-btn').forEach(btn => {
    btn.classList.toggle('active', btn.dataset.lang === lang);
  });

  // Update HTML lang attribute
  document.documentElement.lang = lang === 'tj' ? 'tg' : lang;

  // Update page links to include lang parameter
  updateLinks(lang);
}

function updateLinks(lang) {
  document.querySelectorAll('a[href]').forEach(a => {
    const href = a.getAttribute('href');
    if (href && href.endsWith('.html') || (href && href.includes('.html?'))) {
      const url = new URL(href, window.location.origin + window.location.pathname);
      url.searchParams.set('lang', lang);
      a.setAttribute('href', url.pathname + url.search);
    }
  });
}

function initI18n() {
  setLanguage(currentLang);
  
  document.querySelectorAll('.lang-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      setLanguage(btn.dataset.lang);
    });
  });
}

// Export for use
if (typeof window !== 'undefined') {
  window.CoalForumI18n = { setLanguage, getDefaultLang, initI18n, translations, getCurrentLang: () => currentLang };
}
