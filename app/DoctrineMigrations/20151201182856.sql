
--
-- Dumping data for table `action_labels`
--

INSERT INTO `action_labels` (`id`, `name`, `html_class`) VALUES
(1, 'none', 'none'),
(2, 'Расспродажа', 'flag_sell'),
(3, 'Новинка', 'flag_new');

-- --------------------------------------------------------

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `name`, `description`) VALUES
(1, 'mainMenu', 'Верхнее меню'),
(2, 'sideleftMenu', 'Левое меню'),
(3, 'footerleftMenu', 'Левое меню подвала'),
(4, 'footerrightMenu', 'Правое меню подвала');

-- --------------------------------------------------------

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `menu_id`, `name`, `priority`, `link`, `link_type`, `create_time`, `update_time`, `active`) VALUES
(1, 1, 'Контакты', 5, 'contacts', 'local', '2015-11-23 08:37:19', '2015-11-23 08:37:19', NULL),
(2, 1, 'Кредит', 3, 'info/credit', 'local', '2015-11-23 08:37:19', '2015-11-23 08:37:19', NULL),
(3, 1, 'Гарантия', 4, 'info/guarantee', 'local', '2015-11-23 08:37:19', '2015-11-23 08:37:19', NULL),
(4, 1, 'О нас', 2, 'info/about-us', 'local', '2015-11-23 08:37:19', '2015-11-23 08:37:19', NULL),
(5, 1, 'Доставка и оплата', 1, 'info/shipping-and-payment', 'local', '2015-11-23 08:37:19', '2015-11-23 08:37:19', NULL),
(6, 2, 'Контакты', 5, 'contacts', 'local', '2015-11-23 08:37:19', '2015-11-23 08:37:19', NULL),
(7, 2, 'Кредит', 3, 'info/credit', 'local', '2015-11-23 08:37:19', '2015-11-23 08:37:19', NULL),
(8, 2, 'Гарантия', 4, 'info/guarantee', 'local', '2015-11-23 08:37:19', '2015-11-23 08:37:19', NULL),
(9, 2, 'О нас', 2, 'info/about-us', 'local', '2015-11-23 08:37:19', '2015-11-23 08:37:19', NULL),
(10, 2, 'Доставка и оплата', 1, 'info/shipping-and-payment', 'local', '2015-11-23 08:37:19', '2015-11-23 08:37:19', NULL),
(11, 3, 'Кредит', 3, 'info/credit', 'local', '2015-11-23 08:37:19', '2015-11-23 08:37:19', NULL),
(12, 3, 'О нас', 2, 'info/about-us', 'local', '2015-11-23 08:37:19', '2015-11-23 08:37:19', NULL),
(13, 3, 'Доставка и оплата', 1, 'info/shipping-and-payment', 'local', '2015-11-23 08:37:19', '2015-11-23 08:37:19', NULL),
(14, 4, 'Контакты', 2, 'contacts', 'local', '2015-11-23 08:37:19', '2015-11-23 08:37:19', NULL),
(15, 4, 'Гарантия', 1, 'info/guarantee', 'local', '2015-11-23 08:37:19', '2015-11-23 08:37:19', NULL);

-- --------------------------------------------------------

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `title`, `alias`, `description`, `content`, `seo_title`, `seo_description`, `seo_keywords`, `create_time`, `update_time`) VALUES
(1, 'Кредит', 'credit', 'Кредит', '<div class="credit">	<div class="credit__i">		<div class="credit-info">			<h3>Фішка в кредитуванні</h3>			<div class="row">				<div class="item">					<div class="item__img">						<img src="/img/demo/chart.jpg" alt="#"/>					</div>					<div class="item__text">						<p>Ви можете оформити кредит як на							всю суму покупки, а також на							частину (розмір власного внеску							ви обираєте самі)</p>					</div>				</div>				<div class="item">					<div class="item__img">						<img src="/img/demo/money.jpg" alt="#"/>					</div>					<div class="item__text">						<p>Якщо товар Вам не підійшов і ви							вирішили його повернути в законні							строки то ми повертаємо Вам							кошти і Ви самі вирішуєте чи							погашати кредит, чи купити якусь							іншу річ у нас, чи лишити кошти							собі</p>					</div>				</div>				<div class="item">					<div class="item__img">						<img src="/img/demo/doc.jpg" alt="#"/>					</div>					<div class="item__text">						<p>Підписати Договори Ви можете в							будь-якому відділені Банку де Вам							буде зручніше</p>					</div>				</div>			</div>		</div>		<!--credit-info end-->		<div class="creditor">			<div class="creditor__i">				<h3>Кредит від ПРАВЕКС-БАНКу</h3>				<img src="/img/demo/pravex.jpg" alt="#"/>				<p>Умови кредитування:</p>				<ul>					<li>До 20 000 грн. без довідки про доходи</li>					<li>Вік від 21 до 70 років</li>					<li>Срок - до 60 міс.</li>					<li>Кредит до 50 000 грн.</li>					<li>Без застави та поручительства</li>					<li>Рішення по кредиту за 30 хвилин</li>				</ul>			</div>		</div>	</div></div>', 'Кредит', 'Кредит', 'Кредит', '2015-11-23 08:37:02', '2015-11-23 08:37:02'),
(2, 'Гарантия', 'guarantee', 'Гарантия', '<div class="warranty">	<div class="warranty__i">		<div class="item">			<div class="item__i">				<h3>На какие товары предоставляется гарантия?</h3>				<p>Гарантия предоставляется на все товары за исключением б/у товаров.</p>				<p>Подтверждением прав на гарантийное облуживание является гарантийный талон и товарный чек фиксирующий покупку					в интернет-магазине MyTEX.</p>			</div>		</div><!--item end-->		<div class="item">			<div class="item__i">				<h3>Я могу обменять или вернуть товар?</h3>				<p>Возврат товара осуществляется в соответствии с действующим законодательством Украины — «Закон по защите прав					потребителей. Статья 8», в течении полных 14 календарных дней включительно.</p>			</div>		</div><!--item end-->		<div class="item">			<div class="item__i">				<h3>Чтобы реализовать это право возврата товара, необходимо убедиться, что:</h3>				<ul>— товар не был в использовании, т. е. является новым;					— сохранены товарный вид (отсутствие царапин, сколов, потёртостей и т.д.) и потребительские качества товара;					— сохранены пломбы, ярлыки, товарный чек.</ul>			</div>		</div><!--item end-->		<div class="item">			<div class="item__i">				<h3>ОБРАЩАЕМ ВАШЕ ВНИМАНИЕ!</h3>				<p>Неполадок и несоответствий заявленным требованиям после ухода курьера, возврат товара осуществляется в					соответствии с действующим законодательством Украины — «Закон по защите прав потребителей. Статья 8», в течении					полных 14 календарных дней с момента покупки.</p>			</div>		</div><!--item end-->		<div class="item">			<div class="item__i">				<h3>Где и как можно произвести обмен или возврат?</h3>				<p>Обменять или вернуть товар можно на нашем складе по адресу: г. Хмельницкий, ул. Проскуровского Подполья, 195/1					Для регионов Украины возврат происходит перевозчиками Автолюкс или Нова пошта за счет покупателя.</p>				<p>Для осуществления возврата товара на складе нужно иметь при себе удостоверение личности (паспорт или права) и					товарный чек.</p>				<p>Мы вернем деньги в день возврата или, в случае отсутствия денег в кассе, не позже, чем через 7 дней.</p>			</div>		</div><!--item end-->		<div class="item">			<div class="item__i">				<h3>Кто обеспечивает гарантийное обслуживание?</h3>				<p>Гарантийным обслуживанием занимаются непосредственно авторизированные сервисные центры.</p>				<p>Если сервисные центры отдельных производителей недоступны в Украине по всем вопросам связанным с гарантийным					обслуживанием, обращайтесь к нам.</p>				<p>Право на бесплатное обслуживание дает гарантийный талон, в котором указываются: модель; серийный номер;					гарантийный срок; дата продажи товара; адреса и телефоны сервисных центров.</p>			</div>		</div><!--item end-->		<div class="item">			<div class="item__i">				<h3>На какие товары предоставляется гарантия?</h3>				<p>Гарантия предоставляется на все товары за исключением б/у товаров.</p>				<p>Подтверждением прав на гарантийное облуживание является гарантийный талон и товарный чек фиксирующий покупку					в интернет-магазине MyTEX.</p>			</div>		</div><!--item end-->	</div></div>', 'Гарантия', 'Гарантия', 'Гарантия', '2015-11-23 08:37:02', '2015-11-23 08:37:02'),
(3, 'О нас', 'about-us', 'О нас', '<div class="about">	<div class="about__i">		<p><strong>Интернет-магазин MyTEX предлагает Вам широкий ассортимент товаров разных направлений и стоимости. </strong></p>		<img src="/img/demo/about.jpg" alt="#"/>		<p>Нашей главной задачей мы видим обеспечение максимального комфорта Нашему покупателю независимо от его статуса.			Даже, если вдруг Вы не нашли нужную вещь, наши менеджеры всегда помогут найти достойное решение.</p>		<div class="specials">			<div class="specials__i">				<p>Специально <strong>для Вас</strong> у Нас:</p>				<div class="row">					<div class="item">						<div class="item__i">							<div class="img">								<img src="/img/demo/new-prod.jpg" alt="#"/>							</div>							<div class="text">								<p>ВСЯ НОВАЯ ПРОДУКЦИЯ - только									сертифицированная, со всей									сопутствующей гарантийной									документацией </p>							</div>						</div>					</div><!--item end-->					<div class="item">						<div class="item__i">							<div class="img">								<img src="/img/demo/like.jpg" alt="#"/>							</div>							<div class="text">								<p>ВСЯ ПРОДУКЦИЯ Б/У - тщательно									проверена нашими специалистами</p>							</div>						</div>					</div><!--item end-->					<div class="item">						<div class="item__i">							<div class="img">								<img src="/img/demo/basket.jpg" alt="#"/>							</div>							<div class="text">								<p>Удобные покупки через сайт за счет									легкой навигации, всего несколько									минут и Ваш заказ оформлен								</p>							</div>						</div>					</div><!--item end-->					<div class="item">						<div class="item__i">							<div class="img">								<img src="/img/demo/card.jpg" alt="#"/>							</div>							<div class="text">								<p>Возможность покупки не только									при наличном расчёте, но и									безналичном, а также в кредит с									лояльными условиями (даже б/у									техника)</p>							</div>						</div>					</div><!--item end-->					<div class="item">						<div class="item__i">							<div class="img">								<img src="/img/demo/repair.jpg" alt="#"/>							</div>							<div class="text">								<p>В случае если у Вас возникли									какие-либо неполадки или проблемы									с товаром, ремонт производится в									авторизированных центрах при									нашей поддержке          </p>							</div>						</div>					</div><!--item end-->					<div class="item">						<div class="item__i">							<div class="img">								<img src="/img/demo/fast.jpg" alt="#"/>							</div>							<div class="text">								<p>Мы совершим быструю доставку в									любую точку Украины на склад									перевозчика</p>							</div>						</div>					</div><!--item end-->				</div>			</div>		</div><!--specials end-->		<p>Помимо всех преимуществ, которые Вы получаете в MyTEX, команда нашего магазина преследует цель создания			максимально комфортного сервиса для наших клиентов и посетителей сайта, поэтому мы всегда прислушиваемся к			отзывам и пожеланиям покупателей. </p>	</div></div>', 'О нас', 'О нас', 'О нас', '2015-11-23 08:37:02', '2015-11-23 08:37:02'),
(4, 'Доставка и оплата', 'shipping-and-payment', 'Доставка и оплата', '<div class="delivery">	<div class="delivery__i">		<h3>Способы доставки</h3>		<div class="delivery-row">			<div class="item">				<div class="item__i">					<a href="#delivery-popup-1" class="popup_js">						<span><i class="icon icon-car"></i></span>						<p><strong>Доставка</strong></p>						<p>Доставка товаров осуществляется с помощью							транспортных компаний «Нова пошта» и							«Delivery».</p>					</a>				</div>			</div>			<!--item end-->			<div class="item">				<div class="item__i">					<a href="#delivery-popup-2" class="popup_js">						<span><i class="icon icon-man"></i></span>						<p><strong>Самовывоз</strong></p>						<p>Если Вы хотите забрать заказанный Вами							товар с нашего склада.</p>					</a>				</div>			</div>			<!--item end-->		</div>		<!--delivery-row end-->		<h3>Способы оплаты</h3>		<div class="delivery-row">			<div class="item">				<div class="item__i">					<a href="#pay-popup-1" class="popup_js">						<span><i class="icon icon-money"></i></span>						<p><strong>Наличная</strong></p>						<p>Оплата наличными при доставке.</p>					</a>				</div>			</div>			<!--item end-->			<div class="item">				<div class="item__i">					<a href="#pay-popup-2" class="popup_js">						<span><i class="icon icon-card"></i></span>						<p><strong>Безналичная</strong></p>						<p>При безналичной оплате менеджер магазина							отправляет Вам реквизиты.</p>					</a>				</div>			</div>			<!--item end-->		</div>		<!--delivery-row end-->	</div></div>', 'Доставка и оплата', 'Доставка и оплата', 'Доставка и оплата', '2015-11-23 08:37:02', '2015-11-23 08:37:02');

-- --------------------------------------------------------

--
-- Dumping data for table `site_params`
--

INSERT INTO `site_params` (`id`, `param_name`, `param_value`) VALUES
(1, 'skype', 'my_tex'),
(2, 'email', 'mytex@ukr.net'),
(3, 'copyright', 'Copyright &copy; 2015, My TEX'),
(4, 'phone', '["<strong>097<\\/strong> 783 83 35","<strong>063<\\/strong> 698 15 42","<strong>097<\\/strong> 523 54 85"]'),
(5, 'contacts_phone', '["+38 097 783 83 35","+38 063 698 15 42"]'),
(6, 'stock_adress', 'г. Хмельницкий, ул. Проскуровского Подполья, 195/1'),
(7, 'contacts_map_lat', '49.4261423'),
(8, 'contacts_map_lng', '26.9630859');

-- --------------------------------------------------------

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `create_time`, `update_time`, `username_canonical`, `email_canonical`, `enabled`, `salt`, `last_login`, `locked`, `expired`, `expires_at`, `confirmation_token`, `password_requested_at`, `roles`, `credentials_expired`, `credentials_expire_at`, `uid`, `fio`, `phone`) VALUES
(1, 'admin', 'linux0uid@gmail.com', '$2y$13$ct0hjeqwjeo0k0gwc0ogweLgRGynlnhjVToNtrf3OkujmFmL/Y1wK', '2015-11-24 13:30:47', '2015-11-24 13:30:57', 'admin', 'linux0uid@gmail.com', 1, 'ct0hjeqwjeo0k0gwc0ogwswc0cs0s4c', NULL, 0, 0, NULL, NULL, NULL, 'a:1:{i:0;s:16:"ROLE_SUPER_ADMIN";}', 0, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Dumping data for table `vendors`
--

INSERT INTO `vendors` (`id`, `name`, `priority`, `description`, `create_time`, `update_time`) VALUES
(1, 'Micaela Strosin', 14, 'Est placeat rerum ut et enim ex. Facere sunt quia delectus aut nam et eum. Fugit repellendus illo veritatis.', '2015-11-23 08:37:09', '2015-11-23 08:37:09'),
(2, 'yugcontract', 15, 'Fugiat nemo omnis consequatur. Qui cupiditate eos quod veritatis vel optio provident non. Magnam molestias et quibusdam et. Quo voluptatum quia ipsum.', '2015-11-23 08:37:09', '2015-11-30 12:47:05'),
(3, 'Lavonne Collins', 10, 'Non voluptas fuga non autem. Non explicabo et neque itaque ex quaerat ut aut. Consequatur non rerum in cupiditate voluptas molestiae fuga. Cum non qui quaerat cupiditate incidunt id sunt.', '2015-11-23 08:37:09', '2015-11-23 08:37:09'),
(4, 'Miss Kira Walter DDS', 14, 'Qui doloremque aperiam qui rerum accusamus beatae. Enim et doloribus voluptatibus perspiciatis. Sapiente quia suscipit doloribus.', '2015-11-23 08:37:09', '2015-11-23 08:37:09'),
(5, 'Kamryn Franecki', 13, 'Voluptatem quibusdam ad in maiores nisi. Quibusdam sapiente quia recusandae aut. Laboriosam sint enim reiciendis quod.', '2015-11-23 08:37:09', '2015-11-23 08:37:09');

-- --------------------------------------------------------

--
-- Dumping data for table `product_colors`
--

INSERT INTO `product_colors` (`id`, `name`, `hex`, `create_time`, `update_time`) VALUES
(1, 'none', 'none', '2015-12-01 16:58:38', '2015-12-01 16:58:38');

-- --------------------------------------------------------


