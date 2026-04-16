CREATE DATABASE rebook;
USE rebook;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    id_document CHAR(9) NOT NULL,
    birthday DATE NOT NULL,
    city VARCHAR(50) NOT NULL,
    address VARCHAR(100) NOT NULL,
    postal_code CHAR(5) NOT NULL,
    phone CHAR(9) NOT NULL,
    card_name VARCHAR(100),
    card_number CHAR(16),
    cvv CHAR(3)
);

CREATE TABLE books(
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    author VARCHAR(100),
    description VARCHAR(1000) NOT NULL,
    rent_price DECIMAL(10, 2),
    sell_price DECIMAL (10, 2),
    isbn CHAR(13),
    url VARCHAR(250),
    in_cart BOOL,
    rented BOOL,
    rent_expiration_date DATE,
    sold BOOL,
    category VARCHAR(50),
    id_user INT,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE rented(
    id_user INT,
    id_book INT,
    rented_on DATE,
    expiration_date DATE,
    PRIMARY KEY (id_user, id_book),
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (id_book) REFERENCES books(id) ON DELETE CASCADE
);

CREATE TABLE favourites(
    id_user INT,
    id_book INT,
    PRIMARY KEY (id_user, id_book),
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (id_book) REFERENCES books(id) ON DELETE CASCADE
);

CREATE TABLE carts(
    id_user INT,
    id_book INT,
    PRIMARY KEY (id_user, id_book),
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (id_book) REFERENCES books(id) ON DELETE CASCADE
);

CREATE TABLE bought(
    id_user INT,
    id_book INT,
    PRIMARY KEY (id_user, id_book),
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (id_book) REFERENCES books(id) ON DELETE CASCADE
);

INSERT INTO users (
    email, 
    name, 
    lastname, 
    password, 
    id_document, 
    birthday, 
    city, 
    address, 
    postal_code, 
    phone)
VALUES (
    'rebook@rebook.com',
    'rebook',
    'admin',
    '$2y$10$SSADQ51D0pU.cqZ2/8zvyeT7z8ryJOCUX7AlkwQ6Us2n3xFGrsnqu',
    '11111111A',
    '1999-09-13',
    'Madrid',
    'Calle 123',
    '28935',
    '111111111'
);

INSERT INTO books(
    title,
    author,
    description,
    rent_price,
    sell_price,
    isbn,
    url,
    in_cart,
    rented,
    rent_expired,
    sold,
    category,
    id_user
)
VALUES(
    'El nombre del viento',
    'Patrick Rothfuss',
    'He robado princesas a reyes agónicos. Incendié la ciudad de Trebon. He pasado la noche con Felurian y he despertado vivo y cuerdo. Me expulsaron de la Universidad a una edad a la que a la mayoría todavía no los dejan entrar. He recorrido de noche caminos de los que otros no se atreven a hablar ni siquiera de día. He hablado con dioses, he amado a mujeres y he escrito canciones que hacen llorar a los bardos. Me llamo Kvothe. Quizá hayas oído hablar de mí.',
    7,
    15,
    '9788401337208',
    'https://imagessl8.casadellibro.com/a/l/s7/48/9788401352348.webp',
    0,
    0,
    0,
    0,
    'fatansía',
    1
);
INSERT INTO books(
    title,
    author,
    description,
    rent_price,
    sell_price,
    isbn,
    url,
    in_cart,
    rented,
    rent_expired,
    sold,
    category,
    id_user
)
VALUES(
    'La Comunidad del anillo',
    'J. R. R. Tolkien',
    'En la adormecida e idílica Comarca, un joven hobbit recibe un encargo: custodiar el Anillo Único y emprender el viaje para su destrucción en la Grieta del Destino. Acompañado por magos, hombres, elfos y enanos, atravesará la Tierra Media y se internará en las sombras de Mordor, perseguido siempre por las huestes de Sauron, el Señor Oscuro, dispuesto a recuperar su creación para establecer el dominio definitivo del Mal',
    7,
    15,
    '9788401337208',
    'https://imagessl3.casadellibro.com/a/l/s7/63/9788445000663.webp',
    0,
    0,
    0,
    0,
    'fatansía',
    1
);
INSERT INTO books(
    title,
    author,
    description,
    rent_price,
    sell_price,
    isbn,
    url,
    in_cart,
    rented,
    rent_expired,
    sold,
    category,
    id_user
)
VALUES(
    'Las dos Torres',
    'J. R. R. Tolkien',
    'La Compañía se ha disuelto y sus integrantes emprenden caminos separados. Frodo y Sam continúan solos su viaje a lo largo del río Anduin, perseguidos por la sombra misteriosa de un ser extraño que también ambiciona la posesión del Anillo. Mientras, hombres, elfos y enanos se preparan para la batalla final contra las fuerzas del Señor del Mal.',
    7,
    15,
    '9788401337208',
    'https://imagessl5.casadellibro.com/a/l/s7/35/9788445073735.webp',
    0,
    0,
    0,
    0,
    'fatansía',
    1
);
INSERT INTO books(
    title,
    author,
    description,
    rent_price,
    sell_price,
    isbn,
    url,
    in_cart,
    rented,
    rent_expired,
    sold,
    category,
    id_user
)
VALUES(
    'El retorno del rey',
    'J. R. R. Tolkien',
    'Los ejércitos del Señor Oscuro van extendiendo cada vez más su maléfica sombra por la Tierra Media. Hombres, elfos y enanos unen sus fuerzas para presentar batalla a Sauron y sus huestes. Ajenos a estos preparativos, Frodo y Sam siguen adentrándose en el país de Mordor en su heroico viaje para destruir el Anillo de Poder en las Grietas del Destino.',
    7,
    15,
    '9788401337208',
    'https://imagessl7.casadellibro.com/a/l/s7/87/9788445016787.webp',
    0,
    0,
    0,
    0,
    'fatansía',
    1
);
INSERT INTO books(
    title,
    author,
    description,
    rent_price,
    sell_price,
    isbn,
    url,
    in_cart,
    rented,
    rent_expired,
    sold,
    category,
    id_user
)
VALUES(
    'El temor de un hombre sabio',
    'Patrick Rothfuss',
    'Todo hombre sabio teme tres cosas: la tormenta en el mar, la noche sin luna y la ira de un hombre amable».
El hombre había desaparecido. El mito no. Músico, mendigo, ladrón, estudiante, mago, trotamundos, héroe y asesino, Kvothe había borrado su rastro. Y ni siquiera ahora que le han encontrado, ni siquiera ahora que las tinieblas invaden los rincones del mundo, está dispuesto a regresar. Pero su historia prosigue, la aventura continúa, y Kvothe seguirá contándola para revelar la verdad tras la leyenda.',
    7,
    15,
    '9788401337208',
    'https://imagessl6.casadellibro.com/a/l/s7/36/9788401352836.webp',
    0,
    0,
    0,
    0,
    'fatansía',
    1
);
INSERT INTO books(
    title,
    author,
    description,
    rent_price,
    sell_price,
    isbn,
    url,
    in_cart,
    rented,
    rent_expired,
    sold,
    category,
    id_user
)
VALUES(
    'La música del silencio',
    'Patrick Rothfuss',
    'Al despertar, Auri supo que faltaban siete días. Sí, estaba segura. Él iría a visitarla al séptimo día.»
La Universidad, el bastión del conocimiento, atrae a las mentes más brillantes para aprender ciencias como la artificería y la alquimia. Pero bajo esos edificios y sus concurridas aulas existe un mundo en penumbra.
En ese laberinto de túneles antiguos, de salas y habitaciones abandonadas, de escaleras serpenteantes y pasillos semiderruidos vive Auri, otrora alumna de la Universidad. Ahora cuida de la Subrealidad, de la que ha aprendido que hay misterios que no conviene remover. Ya no se deja engañar por la lógica en la que tanto confían en lo alto: ella sabe reconocer los sutiles peligros y los nombres olvidados que se ocultan bajo la superficie de las cosas.',
    7,
    15,
    '9788401337208',
    'https://imagessl0.casadellibro.com/a/l/s7/60/9788466333160.webp',
    0,
    0,
    0,
    0,
    'fatansía',
    1
);
INSERT INTO books(
    title,
    author,
    description,
    rent_price,
    sell_price,
    isbn,
    url,
    in_cart,
    rented,
    rent_expired,
    sold,
    category,
    id_user
)
VALUES(
    'El estrecho sendero entre deseos',
    'Patrick Rothfuss',
    'Si hay algo que Bast sabe hacer es negociar. Verlo hacer un trato es ver trabajar a un artista..., pero incluso el pincel de un maestro puede errar. Sin embargo, cuando recibe un regalo y lo acepta sin ofrecer nada a cambio, su mundo se tambalea. Pues, aunque sabe regatear, no sabe deberle nada a nadie.
Desde el amanecer a la medianoche, durante el transcurso de un día, seguiremos al fata más encantador de la Crónica del Asesino de Reyes mientras baila con el peligro una y otra vez con asombrosa gracilidad.',
    7,
    15,
    '9788401337208',
    'https://imagessl3.casadellibro.com/a/l/s7/13/9788466378413.webp',
    0,
    0,
    0,
    0,
    'fatansía',
    1
);

INSERT INTO books(
    title,
    author,
    description,
    rent_price,
    sell_price,
    isbn,
    url,
    in_cart,
    rented,
    rent_expired,
    sold,
    category,
    id_user
)
VALUES(
    'El misterioso señor Brown',
    'Agatha Christie',
    'La búsqueda de unos comprometedores documentos secretos, suscritos durante la Primera Guerra Mundial y perdidos en el naufragio del Lusitania, da lugar a una lucha sin cuartel entre los servicios secretos británicos y una banda internacional que quiere utilizar los documentos como instrumento de la propaganda bolchevique. Pero en la vorágine de la guerra de espías aparecen dos jóvenes, Tommy y Tuppence, dispuestos a jugarse la vida para desvelar la identidad del líder de la banda, el misterioso Señor Brown.',
    7,
    15,
    '9788401337208',
    'https://imagessl8.casadellibro.com/a/l/s7/18/9788467082418.webp',
    0,
    0,
    0,
    0,
    'thriller',
    1
);
INSERT INTO books(
    title,
    author,
    description,
    rent_price,
    sell_price,
    isbn,
    url,
    in_cart,
    rented,
    rent_expired,
    sold,
    category,
    id_user
)
VALUES(
    'El cuadro',
    'Agatha Christie',
    'Tommy y Tuppence Beresford visitan a su anciana tía Ada en la residencia donde también habita la señora Lockett, quien menciona un estofado de setas venenosas mientras la señora Lancaster habla de "algo detrás de la chimenea". Tommy y Tuppence se ven repentinamente atrapados en una inesperada aventura en la que afloran una extraña herencia, una casa misteriosa, magia negra, una lápida desaparecida y en la que la vida de Tuppence correrá serio peligro.',
    7,
    15,
    '9788401337208',
    'https://imagessl0.casadellibro.com/a/l/s7/90/9788408322290.webp',
    0,
    0,
    0,
    0,
    'thriller',
    1
);
INSERT INTO books(
    title,
    author,
    description,
    rent_price,
    sell_price,
    isbn,
    url,
    in_cart,
    rented,
    rent_expired,
    sold,
    category,
    id_user
)
VALUES(
    'La señora McGinty ha muerto',
    'Agatha Christie',
    'La señora McGinty, una anciana viuda, ha sido brutalmente asesinada. Todos los indicios apuntan a su inquilino, James Bentley, un hombre huraño y con problemas económicos que ha sido condenado a la horca.',
    7,
    15,
    '9788401337208',
    'https://imagessl2.casadellibro.com/a/l/s7/62/9788467082562.webp',
    0,
    0,
    0,
    0,
    'thriller',
    1
);
INSERT INTO books(
    title,
    author,
    description,
    rent_price,
    sell_price,
    isbn,
    url,
    in_cart,
    rented,
    rent_expired,
    sold,
    category,
    id_user
)
VALUES(
    'El Misterio de Sittaford',
    'Agatha Christie',
    'Una tormenta de nieve ha dejado prácticamente incomunicados a los habitantes de la remota aldea inglesa de Sittaford. Para pasar el tiempo, la señora Willett y sus invitados improvisan una sesión de espiritismo. Lo que empieza como un juego se torna macabro cuando la mesa revela que el capitán Trevelyan ha sido asesinado. Con las carreteras cortadas por la nieve, el comandante Burnaby afronta la peligrosa y larga caminata hasta su casa, donde encuentra el cadáver de su amigo tal y como los espíritus predijeron.',
    7,
    15,
    '9788401337208',
    'https://imagessl3.casadellibro.com/a/l/s7/93/9788467082593.webp',
    0,
    0,
    0,
    0,
    'thriller',
    1
);
INSERT INTO books(
    title,
    author,
    description,
    rent_price,
    sell_price,
    isbn,
    url,
    in_cart,
    rented,
    rent_expired,
    sold,
    category,
    id_user
)
VALUES(
    'Pleamares de la vida',
    'Agatha Christie',
    'En 1944, el millonario Gordon Cloade muere víctima de un bombardeo en Londres. La tragedia se convierte en desastre para su familia cuando la inmensa fortuna pasa a manos de Rosaleen, la enigmática joven con la que se había casado apenas unas semanas antes.',
    7,
    15,
    '9788401337208',
    'https://imagessl7.casadellibro.com/a/l/s7/37/9788467081237.webp',
    0,
    0,
    0,
    0,
    'thriller',
    1
);

INSERT INTO books(
    title,
    author,
    description,
    rent_price,
    sell_price,
    isbn,
    url,
    in_cart,
    rented,
    rent_expired,
    sold,
    category,
    id_user
)
VALUES(
    'No tengas miedo',
    'Stephen King',
    'Cuando el Departamento de Policía de Buckeye City recibe una carta de alguien que pretende «matar a trece inocentes y a un culpable» para expiar una muerte innecesaria, la detective Izzy Jaynes no sabe qué pensar. ¿Están a punto de asesinar a catorce personas por venganza? Preocupada, decide acudir a Holly Gibney para que la ayude.',
    7,
    15,
    '9788401337208',
    'https://imagessl4.casadellibro.com/a/l/s7/14/9788466389914.webp',
    0,
    0,
    0,
    0,
    'terror',
    1
);
INSERT INTO books(
    title,
    author,
    description,
    rent_price,
    sell_price,
    isbn,
    url,
    in_cart,
    rented,
    rent_expired,
    sold,
    category,
    id_user
)
VALUES(
    'La milla verde',
    'Stephen King',
    'Los condenados a muerte aguardan el momento de ser conducidos a la silla eléctrica. Los crímenes abominables que han cometido les convierten en carnaza de un sistema legal que se alimenta de un círculo de locura, muerte y venganza.',
    7,
    15,
    '9788401337208',
    'https://imagessl4.casadellibro.com/a/l/s7/34/9788497592734.webp',
    0,
    0,
    0,
    0,
    'terror',
    1
);
INSERT INTO books(
    title,
    author,
    description,
    rent_price,
    sell_price,
    isbn,
    url,
    in_cart,
    rented,
    rent_expired,
    sold,
    category,
    id_user
)
VALUES(
    'Casa negra',
    'Stephen King',
    'Hace veinte años, Jack Sawyer viajó a un universo paralelo llamado los Territorios para salvar a su madre de la muerte. Ahora es un detective retirado que vive en el pueblo de Tamarack, Wisconsin. No recuerda sus aventuras en los Territorios y se vio obligado a abandonar el cuerpo de policía cuando un extraño suceso amenazó con despertar esos recuerdos.',
    7,
    15,
    '9788401337208',
    'https://imagessl0.casadellibro.com/a/l/s7/90/9788401040290.webp',
    0,
    0,
    0,
    0,
    'terror',
    1
);
INSERT INTO books(
    title,
    author,
    description,
    rent_price,
    sell_price,
    isbn,
    url,
    in_cart,
    rented,
    rent_expired,
    sold,
    category,
    id_user
)
VALUES(
    'IT',
    'Stephen King',
    '¿Quién o qué mutila y mata a los niños de un pequeño pueblo norteamericano?¿Por qué llega cíclicamente el horror a Derry en forma de un payaso siniestro que va sembrando la destrucción a su paso?',
    7,
    15,
    '9788401337208',
    'https://imagessl7.casadellibro.com/a/l/s7/47/9788466345347.webp',
    0,
    0,
    0,
    0,
    'terror',
    1
);
INSERT INTO books(
    title,
    author,
    description,
    rent_price,
    sell_price,
    isbn,
    url,
    in_cart,
    rented,
    rent_expired,
    sold,
    category,
    id_user
)
VALUES(
    'Si te gusta la oscuridad',
    'Stephen King',
    'El nuevo lanzamiento del Rey del Terror es una colección de doce relatos que lo afianza, una vez más, como uno de los mejores narradores de nuestro tiempo.',
    7,
    15,
    '9788401337208',
    'https://imagessl5.casadellibro.com/a/l/s7/15/9788466378215.webp',
    0,
    0,
    0,
    0,
    'terror',
    1
);
INSERT INTO books(
    title,
    author,
    description,
    rent_price,
    sell_price,
    isbn,
    url,
    in_cart,
    rented,
    rent_expired,
    sold,
    category,
    id_user
)
VALUES(
    'El resplandor',
    'Stephen King',
    'REDRUM
Esa es la palabra que Danny había visto en el espejo. Y, aunque no sabía leer, entendió que era un mensaje de horror.
Danny tenía cinco años, y a esa edad poco niños saben que los espejos invierten las imágenes y menos aún saben diferenciar entre realidad y fantasía. Pero Danny tenía pruebas de que sus fantasías relacionadas con el resplandor del espejo acabarían cumpliéndose: REDRUM... MURDER, asesinato.
Pero su padre necesitaba aquel trabajo en el hotel. Danny sabía que su madre pensaba en el divorcio y que su padre se obsesionaba con algo muy malo, tan malo como la muerte y el suicidio. Sí, su padre necesitaba aceptar la propuesta de cuidar de aquel hotel de lujo de más de cien habitaciones, aislado por la nieve durante seis meses. Hasta el deshielo iban a estar solos. ¿Solos?...',
    7,
    15,
    '9788401337208',
    'https://imagessl9.casadellibro.com/a/l/s7/19/9788466357319.webp',
    0,
    0,
    0,
    0,
    'terror',
    1
);

INSERT INTO books(
    title,
    author,
    description,
    rent_price,
    sell_price,
    isbn,
    url,
    in_cart,
    rented,
    rent_expired,
    sold,
    category,
    id_user
)
VALUES(
    'Dune',
    'Frank Herbert',
    'En el desértico planeta Arrakis, el agua es el bien más preciado y llorar a los muertos, el símbolo de máxima prodigalidad. Pero algo hace de Arrakis una pieza estratégica para los intereses del Emperador, las Grandes Casas y la Cofradía, los tres grandes poderes de la galaxia. Arrakis es el único origen conocido de la melange, preciosa especia y uno de los bienes más codiciados del universo.',
    7,
    15,
    '9788401337208',
    'https://imagessl9.casadellibro.com/a/l/s7/79/9788466353779.webp',
    0,
    0,
    0,
    0,
    'ciencia ficción',
    1
);
INSERT INTO books(
    title,
    author,
    description,
    rent_price,
    sell_price,
    isbn,
    url,
    in_cart,
    rented,
    rent_expired,
    sold,
    category,
    id_user
)
VALUES(
    'El mesías de Dune',
    'Frank Herbert',
    'Arrakis, también llamado Dune: un mundo desierto en pos del sueño de convertirse en un paraíso, cuna de mil guerras que se han extendido por todo el universo y de un anhelo mesiánico que intenta alcanzar el sueño más antiguo de la humanidad...',
    7,
    15,
    '9788401337208',
    'https://imagessl1.casadellibro.com/a/l/s7/61/9788466356961.webp',
    0,
    0,
    0,
    0,
    'ciencia ficción',
    1
);
INSERT INTO books(
    title,
    author,
    description,
    rent_price,
    sell_price,
    isbn,
    url,
    in_cart,
    rented,
    rent_expired,
    sold,
    category,
    id_user
)
VALUES(
    'Hijos de Dune',
    'Frank Herbert',
    'Leto Atreides, el hijo de Paul -el mesías de una religión que arrasó el universo, el mártir que, ciego, se adentró en el desierto para morir-, tiene ahora nueve años. Pero es mucho más que un niño, porque dentro de él laten miles de vidas que lo arrastran a un implacable destino. Él y su hermana gemela, bajo la regencia de su tía Alia, gobiernan un planeta que se ha convertido en el eje de todo el universo. Arrakis, más conocido como Dune.
Y en este planeta, centro de las intrigas de una corrupta clase política y sometido a una sofocante burocracia religiosa, aparece de pronto un predicador ciego, procedente del desierto. ¿Es realmente Paul Atreides, que regresa de entre los muertos para advertir a la humanidad del peligro más abominable?',
    7,
    15,
    '9788401337208',
    'https://imagessl5.casadellibro.com/a/l/s7/05/9788466357005.webp',
    0,
    0,
    0,
    0,
    'ciencia ficción',
    1
);
INSERT INTO books(
    title,
    author,
    description,
    rent_price,
    sell_price,
    isbn,
    url,
    in_cart,
    rented,
    rent_expired,
    sold,
    category,
    id_user
)
VALUES(
    'Dios emperador de Dune',
    'Frank Herbert',
    'Esta cuarta entrega de la saga «Dune» centra su trama en la figura mesiánica de Leto Atreides II (hijo de Paul Atreides, héroe cuya estirpe hunde sus raíces en la legendaria casa griega de los Atridas) y nos lleva, a través de diversos dilemas éticos, a comprender los mitos que necesita la humanidad y a los héroes que los encarnan. El futuro, en el mundo de Dune, pertenece solo a los que son capaces de pensar por sí mismos.',
    7,
    15,
    '9788401337208',
    'https://imagessl3.casadellibro.com/a/l/s7/43/9788466359443.webp',
    0,
    0,
    0,
    0,
    'ciencia ficción',
    1
);
INSERT INTO books(
    title,
    author,
    description,
    rent_price,
    sell_price,
    isbn,
    url,
    in_cart,
    rented,
    rent_expired,
    sold,
    category,
    id_user
)
VALUES(
    'Herejes de Dune',
    'Frank Herbert',
    'Esta quinta entrega de la serie prosigue con las aventuras de la estirpe de los Atreides en el fascinante planeta de arena. Nos hallamos en el futuro respecto a la acción de Dios emperador de Dune.',
    7,
    15,
    '9788401337208',
    'https://imagessl9.casadellibro.com/a/l/s7/99/9788466359399.webp',
    0,
    0,
    0,
    0,
    'ciencia ficción',
    1
);
INSERT INTO books(
    title,
    author,
    description,
    rent_price,
    sell_price,
    isbn,
    url,
    in_cart,
    rented,
    rent_expired,
    sold,
    category,
    id_user
)
VALUES(
    'Casa Capitular',
    'Frank Herbert',
    'Las Honorables Madres se enfrentan, con sus terribles poderes, a la secular Bene Gesserit. Las revenidas Madres, ocultas y fortificadas en su planeta Casa Capitular, intentan revivir el viejo orden que les dio su antiguo poder en todo el universo. Un ghola de Miles Teg está siendo adiestrado para superar incluso a su poderoso antecesor.
La unión de Duncan Idaho y Murbella, cautivos ambos en la no-nave, puede arrojar luz sobre el traumático fenómeno de la Dispersión.',
    7,
    15,
    '9788401337208',
    'https://imagessl0.casadellibro.com/a/l/s7/50/9788466359450.webp',
    0,
    0,
    0,
    0,
    'ciencia ficción',
    1
);