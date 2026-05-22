<?php

// Обработчик запросов методом GET для главной страницы портфолио.
function front_get($request) {
  return render_portfolio();
}

// Обработчик запросов методом POST для главной страницы (обработка форм).
function front_post($request) {
    if (empty($request['post'])) {
        return not_found();
    }

    $name = trim($request['post']['field_vashe_imya'][0]['value'] ?? '');
    $phone = trim($request['post']['field_telefon'][0]['value'] ?? '');
    $email = trim($request['post']['field_e_mail'][0]['value'] ?? '');
    $message = trim($request['post']['field_vash_'][0]['value'] ?? '');
    $agreed = isset($request['post']['fz152_agreement']) && $request['post']['fz152_agreement'] == '1';

    $errors = array();
    if ($name === '') {
        $errors[] = 'Пожалуйста, укажите ваше имя.';
    }
    if ($phone === '') {
        $errors[] = 'Пожалуйста, укажите телефон.';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Пожалуйста, укажите корректный E-mail.';
    }
    if ($message === '') {
        $errors[] = 'Пожалуйста, оставьте текст сообщения.';
    }
    if (!$agreed) {
        $errors[] = 'Для отправки заявки необходимо принять соглашение о персональных данных.';
    }

    if (!empty($errors)) {
        return render_portfolio('Ошибка: ' . implode(' ', $errors));
    }

    require_once __DIR__ . '/../scripts/db.php';
    initDB();
    $pdo = getDB();

    $stmt = $pdo->prepare('INSERT INTO contact_requests (name, phone, email, message, agreed) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$name, $phone, $email, $message, $agreed ? 1 : 0]);

    return render_portfolio('Спасибо! Ваша заявка принята и сохранена в базе данных.');
}

// Функция для отрисовки основного контента портфолио
function render_portfolio($notice = '') {
    $noticeHtml = '';
    if ($notice !== '') {
        $noticeHtml = '<div style="margin: 1rem auto; padding: 1rem 1.25rem; max-width: 1100px; background: #e8f8f1; border: 1px solid #80c7a3; color: #0d3f2a; border-radius: 8px;">' . htmlspecialchars($notice) . '</div>';
    }

    return <<<HTML
    $noticeHtml
    <div class="mask"></div>
    <div class="mask__area">
        <video autoplay muted loop id="myVideo">
            <source src="img/video.mp4" type="video/mp4">
        </video>
    </div>

    <nav class="main__section-nav">
        <img src="img/main-logo.svg" alt="#" class="logo">
        <div class="hamburger-menu" id="hamburger-menu">
            <div class="hamburger"></div>
        </div>
    </nav>
    <ul class="nav-links" id="nav-links">
        <li><a href="#help">ПОДДЕРЖКА DRUPAL</a></li>
        <li><a href="./index.php?q=admin" id="admnstr">АДМИНИСТРИРОВАНИЕ</a></li>
        <ul id="admnstr__list">
            <li><a href="#block-copyright">МИГРАЦИЯ</a></li>
            <li><a href="#block-copyright">БЭКАПЫ</a></li>
            <li><a href="#block-copyright">АЙДИТ БЕЗОПАСНОСТЬ</a></li>
            <li><a href="#block-copyright">ОПТИМИЗАЦИЯ СКОРОСТИ</a></li>
            <li><a href="#block-copyright">ПЕРЕЕЗД НА HTTPS</a></li>
        </ul>
        <li><a href="#cases">ПРОДВИЖЕНИЕ</a></li>
        <li><a href="#cases">РЕКЛАМА</a></li>
        <li><a href="#" id="aboutUs">О НАС</a></li>
        <ul id="aboutUs__list">
            <li><a href="#team">КОМАНДА</a></li>
            <li><a href="#team">DRUPALGIVE</a></li>
            <li><a href="#reviews">БЛОГ</a></li>
            <li><a href="#cases">КУРСЫ DRUPAL</a></li>
        </ul>
        <li><a href="#cases">ПРОЕКТЫ</a></li>
        <li><a href="#block-copyright">КОНТАКТЫ</a></li>
    </ul>

    <section id="main__section"  class="container">
        <div class="container">
            <nav class="navigation">
                <div class="logo">
                    <img src="img/main-logo.svg" alt="#">
                </div>
                <ul class="navigation__list">
                    <li class="navigation__item"><a href="#help" class="navigation__link">ПОДДЕРЖКА DRUPAL</a></li>
                    <li class="navigation__item"><a href="./index.php?q=admin" class="navigation__link" id="admnstr_desct">АДМИНИСТРИРОВАНИЕ</a>
                    <ul id="admnstr__desct-list">
                        <li><a href="#block-copyright">МИГРАЦИЯ</a></li>
                        <li><a href="#block-copyright">БЭКАПЫ</a></li>
                        <li><a href="#block-copyright">АЙДИТ БЕЗОПАСНОСТЬ</a></li>
                        <li><a href="#block-copyright">ОПТИМИЗАЦИЯ СКОРОСТИ</a></li>
                        <li><a href="#block-copyright">ПЕРЕЕЗД НА HTTPS</a></li>
                    </ul></li>
                    <li class="navigation__item"><a href="#cases" class="navigation__link">ПРОДВИЖЕНИЕ</a></li>
                    <li class="navigation__item"><a href="#cases" class="navigation__link">РЕКЛАМА</a></li>
                    <li class="navigation__item"><a href="#" class="navigation__link" id="aboutUs__desct">О НАС</a>
                    <ul id="aboutUs__desct-list">
                        <li><a href="#team">КОМАНДА</a></li>
                        <li><a href="#team">DRUPALGIVE</a></li>
                        <li><a href="#reviews">БЛОГ</a></li>
                        <li><a href="#cases">КУРСЫ DRUPAL</a></li>
                    </ul></li>
                    <li class="navigation__item"><a href="#cases" class="navigation__link">ПРОЕКТЫ</a></li>
                    <li class="navigation__item"><a href="#block-copyright" class="navigation__link">КОНТАКТЫ</a></li>
                </ul>
            </nav>
        </div>
        
        <div class="main__section-container wrapper" >
            <div class="main__block">
                <div class="main__title">Поддержка <br> сайтов на Drupal</div>
                <div class="main__subtitle">Сопровождение и подержка сайтов<br>на CMS Drupal любых версий и запущенности</div>
                <button class="main__tariff-btn">
                    <a href="#tariff" class="main__tariff-link">ТАРИФЫ</a>
                </button>
            </div>
            
            <div class="main__adv">
                <div class="advantages">
                    <div class="advantages__item">
                        <div class="advantages__title-wrapper">
                            <div class="advantages__title-1">
                                <div class="advantages__title advantages__title-1">#1</div>
                                <div class="advantages__cup">
                                <img src="img/cup.png" alt="#">
                                </div>
                            </div>
                        </div>
                        <div class="advantages__subtitle">Drupal-разработчик <br>в России по версии <br> Рейтинга Рунета</div>
                    </div>
                    <div class="advantages__item">
                        <div class="advantages__title">3+</div>
                        <div class="advantages__subtitle">средний опыт<br> специалистов более<br>3 лет</div>
                    </div>
                    <div class="advantages__item">
                        <div class="advantages__title">14</div>
                        <div class="advantages__subtitle">лет опыта в сфере <br>Drupal</div>
                    </div>
                    <div class="advantages__item">
                        <div class="advantages__title">50+</div>
                        <div class="advantages__subtitle">модулей и тем
                            <br>в формате DrupalGive</div>
                    </div>
                    <div class="advantages__item">
                        <div class="advantages__title">90 000+</div>
                        <div class="advantages__subtitle">часов поддержки <br>сайтов на Drupal</div>
                    </div>
                    <div class="advantages__item">
                        <div class="advantages__title">300+</div>
                        <div class="advantages__subtitle">Проектов<br>на поддержке</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="adv">
        <div class="container">
            <h2 class="adv__title" >13  лет совершенствуем <br>компетенции в Drupal <br>поддержке!</h2>
            <div class="adv__subtitle">Разрабатываем и оптимизируем модули,<br> расширяем <br>функциональность сайтов, обновляем дизайн</div>

            <div class="advv__wrapper">
                <div class="adv__item">
                    <div class="drupal__logo">
                        <img src="img/druapl.svg" alt="#" class="drupal">
                        <div class="adv__img"><img src="img/competency-1.svg" alt=""></div>
                    </div>
                    <div class="adv__descr">Добавление <br> информации на сайт, <br>создание новых<br>разделов</div>
                </div>
                <div class="adv__item">
                    <div class="drupal__logo">
                        <img src="img/druapl.svg" alt="#" class="drupal">
                        <div class="adv__img"><img src="img/competency-2.svg" alt=""></div>
                    </div>
                    <div class="adv__descr">Разработка
                        и <br>оптимизация модулей <br>сайта</div>
                </div>
                <div class="adv__item">
                    <div class="drupal__logo">
                        <img src="img/druapl.svg" alt="#" class="drupal">
                        <div class="adv__img"><img src="img/competency-3.svg" alt=""></div>
                    </div>
                    <div class="adv__descr">Интеграция с CRM, 1C, <br>платежными системами, <br>любыми веб-сервисами</div>
                </div>
                <div class="adv__item">
                    <div class="drupal__logo"> 
                        <img src="img/druapl.svg" alt="#" class="drupal">
                        <div class="adv__img"><img src="img/competency-4.svg" alt=""></div>
                    </div>
                    <div class="adv__descr">Любые доработки <br>функционала
                        и дизайна</div>
                </div>
                <div class="adv__item">
                    <div class="drupal__logo">
                        <img src="img/druapl.svg" alt="#" class="drupal">
                        <div class="adv__img"><img src="img/competency-5.svg" alt=""></div>
                    </div>
                    <div class="adv__descr">Аудит и мониторинг <br>безопасности Drupal <br>сайтов</div>
                </div>
                <div class="adv__item">
                    <div class="drupal__logo">
                        <img src="img/druapl.svg" alt="#" class="drupal">
                        <div class="adv__img"><img src="img/competency-6.svg" alt=""></div>
                    </div>
                    <div class="adv__descr">Миграция, импорт <br>контента и апгрейд <br>Drupal</div>
                </div>
                <div class="adv__item">
                    <div class="drupal__logo">
                        <img src="img/druapl.svg" alt="#" class="drupal">
                        <div class="adv__img"><img src="img/competency-7.svg" alt=""></div>
                    </div>
                    <div class="adv__descr">Оптимизация
                        и <br>ускорение Drupal-сайтов</div>
                </div>
                <div class="adv__item">
                    <div class="drupal__logo">
                        <img src="img/druapl.svg" alt="#" class="drupal">
                        <div class="adv__img"><img src="img/competency-8.svg" alt=""></div>
                    </div>
                    <div class="adv__descr">Веб-маркетинг, <br>консультации
                        и работы <br>по SEO</div>
                </div>
            </div>
        </div>
    </section>

    <section class="position">
        <div class="container">
            <section id = "help"></section>
            <h2 class="center_block_title">Поддержка<br>от Drupal-coder</h2>
            <div class="support">
                <div class="support__item">
                    <div class="support__num">01.</div>
                    <h4 class="support__title">Постановка задачи по Email</h4>
                    <div class="support__descr">Удобная и привычная модель постановки задач, при которой задачи фиксируются и никогда не теряются.</div>
                    <div class="support__img"><img src="img/support1.svg" alt=""></div>
                </div>
                <div class="support__item">
                    <div class="support__num">02.</div>
                    <h4 class="support__title">Система Helpdesk – отчетность, прозрачность</h4>
                    <div class="support__descr">Возможность посмотреть все заявки в работе и отработанные часы в личном кабинете через браузер.</div>
                    <div class="support__img"><img src="img/support2.svg" alt=""></div>
                </div>
                <div class="support__item">
                    <div class="support__num">03.</div>
                    <h4 class="support__title">Расширенная техническая поддержка</h4>
                    <div class="support__descr">Возможность организации расширенной техподдержки с 6:00 до 22:00 без выходных.</div>
                    <div class="support__img"><img src="img/support3.svg" alt=""></div>
                </div>
                <div class="support__item">
                    <div class="support__num">04.</div>
                    <h4 class="support__title">Персональный менеджер проекта</h4>
                    <div class="support__descr">Ваш менеджер проекта всегда в курсе текущего состояния проекта и в любой момент готов ответить на любые вопросы.</div>
                    <div class="support__img"><img src="img/support4.svg" alt=""></div>
                </div>
            
                <div class="support__item">
                    <div class="support__num">05.</div>
                    <h4 class="support__title">Удобные способы оплаты</h4>
                    <div class="support__descr">Безналичный расчет по договору или электронные деньги: WebMoney, Яндекс.Деньги, Paypal.</div>
                    <div class="support__img"><img src="img/support5.svg" alt=""></div>
                </div>
                <div class="support__item">
                    <div class="support__num">06.</div>
                    <h4 class="support__title">Работаем с SLA и NDA</h4>
                    <div class="support__descr">Работа в рамках соглашений о конфиденциальности и об уровне качества работ.</div>
                    <div class="support__img"><img src="img/support6.svg" alt=""></div>
                </div>
                <div class="support__item">
                    <div class="support__num">07.</div>
                    <h4 class="support__title">Штатные специалисты</h4>
                    <div class="support__descr">Надежные штатные специалисты, никаких фрилансеров.</div>
                    <div class="support__img"><img src="img/support7.svg" alt=""></div>
                </div>
                <div class="support__item">
                    <div class="support__num">08.</div>
                    <h4 class="support__title">Удобные каналы связи</h4>
                    <div class="support__descr">Консультации по телефону, скайпу, в месенджерах.</div>
                    <div class="support__img"><img src="img/support8.svg" alt=""></div>
                </div>
            </div> 
        </div>
    </section>

    <section id="tariff">
        <div id="app">
            <div class="tariff__background">
                <img src="img/D-background.svg" alt="">
            </div>
            <div class="container">
                <h2 class="center_block_title">Тарифы</h2>
                <div class="tariff__wrapper">
                    <div class="tariff__item">
                        <div class="tariff__item-title">Стартовый</div>
                        <ul class="tariff__item-descr">
                            <li class="tariff__item-descr-item">Консультации и работы по SEO</li>
                            <li class="tariff__item-descr-item">Услуги дизайнера</li>
                            <li class="tariff__item-descr-item">Неиспользованные оплаченные часы переносятся на следующий месяц</li>
                            <li class="tariff__item-descr-item">Предоплата от 6 000 рублей в месяц</li>
                        </ul>
                        <button class="tariff__item-contact-btn show-modal" @click="showModal = true" >СВЯЖИТЕСЬ С НАМИ</button>
                    </div>
                    <div class="tariff__item">
                        <div class="tariff__item-title">Бизнес</div>
                        <ul class="tariff__item-descr">
                            <li class="tariff__item-descr-item">Консультации и работы по SEO</li>
                            <li class="tariff__item-descr-item">Услуги дизайнера</li>
                            <li class="tariff__item-descr-item">Высокое время реакции - до 2 рабочих дней</li>
                            <li class="tariff__item-descr-item">Неиспользованные оплаченные часы не переносятся на следующий месяц</li>
                            <li class="tariff__item-descr-item">Предоплата от 30 000 рублей в месяц</li>
                        </ul>
                        <button class="tariff__item-contact-btn show-modal" @click="showModal = true">СВЯЖИТЕСЬ С НАМИ</button>
                    </div>
                    <div class="tariff__item">
                        <div class="tariff__item-title">VIP</div>
                        <ul class="tariff__item-descr">
                            <li class="tariff__item-descr-item">Консультации и работы по SEO</li>
                            <li class="tariff__item-descr-item">Услуги дизайнера</li>
                            <li class="tariff__item-descr-item">Максимальное время реакции - в день обращения</li>
                            <li class="tariff__item-descr-item">Неиспользованные оплаченные часы не переносятся на следующий месяц</li>
                            <li class="tariff__item-descr-item">Предоплата от 270 000 рублей в месяц</li>
                        </ul>
                        <button class="tariff__item-contact-btn show-modal" @click="showModal = true">СВЯЖИТЕСЬ С НАМИ</button>
                    </div>
                </div>
                <div class="tariff__footer">
                    <p>Вам не подходят наши тарифы? Оставьте заявку и мы <br>предложим вам индивидуальные условия!</p>
                    <a href="#">ПОЛУЧИТЬ ИНДИВИДУАЛЬНЫЙ ТАРИФ</a>
                </div>
            </div>
            <modal v-if="showModal" @close="showModal = false"></modal>
        </div>
        <script>
            Vue.component("modal", {
              template: "#modal-template"
            });
            new Vue({
              el: "#app",
              data: {
                showModal: false
              }
            });
        </script>
    </section>

    <section id="reviews" class="container">
        <h2 class="center_block_title">Отзывы</h2>
        <div class="reviews_wrapper">
            <div class="review_block">
                <div class="review_body_block">
                    <div class="review_text">
                        <div class="review_logo">
                            <img src="img/logo_0.png" alt="#" class="logo_0">
                        </div>
                        <h4 id="comment" class="case_title">Долгие поиски единственного и неповторимого мастера на многострадальный сайт www.cielparfum.com, который был собран крайне некомпетентным программистом и раз в месяц стабильно грозил погибнуть, привели меня на сайт и, в итоге, к ребятам из Drupal-coder. И вот уже практически полгода как не проходит и дня, чтобы я не поудивлялась и не порадовалась своему везению! Ребята доказали, что эта CMS - мощная и грамотная система управления. Надеюсь, что наше сотрудничество затянется надолго! Спасибо!!!
                        </h4>
                        <div  class="review_author_name">
                            С уважением, Наталья Сушкова руководитель Отдела веб-проектов
                            <a href="#">http://www.cielparfum.com/</a>
                        </div>
                    </div>
                    <div class="review_scroll_block">
                        <div class="review_scroll">
                            <button type="button" id="slider1__prev" data-role="none" class="slick1-prev slick-arrow" aria-label="Previous" >&#8592;</button>
                            <span class="slick1-slide-num"><span id="slider1__position" class="slick1-slide-num-current">01</span> / 08</span>
                            <button type="button" data-role="none" id="slider1__next" class="slick1-next slick-arrow" aria-label="Next" >&#8594;</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="work_with_us" class="container">
        <h2 class="center_block_title">С нами работают</h2>
        <div class="work_with_us-text">         
            Десятки компаний доверяют нам самое ценное, что у них есть в интернете – свои сайты. Мы делаем всё, чтобы наше сотрудничество было долгим.
        </div>
    </section>

    <footer>
        <div class="footer_wrapper container">
            <div class="footer_item_wrapper">
                <div class="footer_item">
                    <div class="block-form-title"><h2 class="center_block_title">Оставить заявку на поддержку сайта</h2></div>
                    <div class="block-form-description">Срочно нужна поддержка сайта? Ваша команда не успевает справиться самостоятельно или предыдущий подрядчик не справился с работой? Тогда вам точно к нам! Просто оставьте заявку и наш менеджер с вами свяжется!</div>
                    <div class="block-form-contacts">
                        <ul>
                            <li class="block-form-phone"><a href="tel:88002222673">8 800 222-26-73</a></li>
                            <li class="block-form-email"><a href="mailto:info@drupal-coder.ru">info@drupal-coder.ru</a></li>
                        </ul>
                   </div>
                </div>
            </div>
        </div>
        <section id="block-copyright" class="block clearfix container">
            <div class="field field--name-body field--type-text-with-summary field--label-hidden field--item">
                <div class="social-links">
                    <ul class="social-links-wrapper">
                        <li class="social-links-item">
                            <a title="Вконтакте" target="_blank" href="https://vk.com/initlab">
                            <img alt="Вконтакте" width="16" height="16" style="width: 16px; " src="img/vk1.png"></a>
                        </li>
                        <li class="social-links-item">
                            <a target="_blank" href="https://teleg.one/initlabbot" title="Telegram">
                            <img alt="FAQ" width="16" height="16" style="width: 16px; " src="img/telegram1.png"></a>
                        </li>
                        <li class="social-links-item">
                            <a title="YouTube" target="_blank" href="https://www.youtube.com/channel/UCyFEbngMB-bK2ulNtd_W43A">
                            <img alt="Ютуб" width="16" height="16" style="width: 16px;" src="img/youtube1.png"></a>
                        </li>
                    </ul>
                </div>
                <p>ООО «Инитлаб», Краснодар, Россия.<br>
                  Drupal является зарегистрированной торговой маркой Dries Buytaert.
                </p>
            </div>
        </section>
    </footer>

    <script src="main.js"></script>
    <script src="slider.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script src="jquery.js"></script>
HTML;
}
