--
-- PostgreSQL database dump
--

-- Dumped from database version 16.9 (Debian 16.9-1.pgdg120+1)
-- Dumped by pg_dump version 16.9 (Ubuntu 16.9-0ubuntu0.24.04.1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: public; Type: SCHEMA; Schema: -; Owner: smartlearn_iaqb_user
--

-- *not* creating schema, since initdb creates it


ALTER SCHEMA public OWNER TO smartlearn_iaqb_user;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: admins; Type: TABLE; Schema: public; Owner: smartlearn_iaqb_user
--

CREATE TABLE public.admins (
    id integer NOT NULL,
    username character varying(255) NOT NULL,
    password text NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.admins OWNER TO smartlearn_iaqb_user;

--
-- Name: admins_id_seq; Type: SEQUENCE; Schema: public; Owner: smartlearn_iaqb_user
--

CREATE SEQUENCE public.admins_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.admins_id_seq OWNER TO smartlearn_iaqb_user;

--
-- Name: admins_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: smartlearn_iaqb_user
--

ALTER SEQUENCE public.admins_id_seq OWNED BY public.admins.id;


--
-- Name: messages; Type: TABLE; Schema: public; Owner: smartlearn_iaqb_user
--

CREATE TABLE public.messages (
    id integer NOT NULL,
    task_id integer NOT NULL,
    sender_role character varying(10),
    message text NOT NULL,
    sent_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    type character varying(20) DEFAULT 'Other'::character varying,
    file_path text,
    seen_by_admin boolean DEFAULT false,
    seen_by_student boolean DEFAULT false,
    sender_id character varying(50),
    sender_name character varying(100),
    CONSTRAINT messages_sender_role_check CHECK (((sender_role)::text = ANY ((ARRAY['student'::character varying, 'admin'::character varying])::text[])))
);


ALTER TABLE public.messages OWNER TO smartlearn_iaqb_user;

--
-- Name: messages_id_seq; Type: SEQUENCE; Schema: public; Owner: smartlearn_iaqb_user
--

CREATE SEQUENCE public.messages_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.messages_id_seq OWNER TO smartlearn_iaqb_user;

--
-- Name: messages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: smartlearn_iaqb_user
--

ALTER SEQUENCE public.messages_id_seq OWNED BY public.messages.id;


--
-- Name: questions; Type: TABLE; Schema: public; Owner: smartlearn_iaqb_user
--

CREATE TABLE public.questions (
    id integer NOT NULL,
    student_id integer NOT NULL,
    title character varying(255) NOT NULL,
    pages integer NOT NULL,
    description text NOT NULL,
    other_info text,
    file_path character varying(255),
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    price numeric(10,2) DEFAULT 1 NOT NULL,
    student_name character varying(100),
    question_text text
);


ALTER TABLE public.questions OWNER TO smartlearn_iaqb_user;

--
-- Name: questions_id_seq; Type: SEQUENCE; Schema: public; Owner: smartlearn_iaqb_user
--

CREATE SEQUENCE public.questions_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.questions_id_seq OWNER TO smartlearn_iaqb_user;

--
-- Name: questions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: smartlearn_iaqb_user
--

ALTER SEQUENCE public.questions_id_seq OWNED BY public.questions.id;


--
-- Name: students; Type: TABLE; Schema: public; Owner: smartlearn_iaqb_user
--

CREATE TABLE public.students (
    id integer NOT NULL,
    name character varying(100) NOT NULL,
    email character varying(100),
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.students OWNER TO smartlearn_iaqb_user;

--
-- Name: students_id_seq; Type: SEQUENCE; Schema: public; Owner: smartlearn_iaqb_user
--

CREATE SEQUENCE public.students_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.students_id_seq OWNER TO smartlearn_iaqb_user;

--
-- Name: students_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: smartlearn_iaqb_user
--

ALTER SEQUENCE public.students_id_seq OWNED BY public.students.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: smartlearn_iaqb_user
--

CREATE TABLE public.users (
    id integer NOT NULL,
    full_name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    password text NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    name character varying(100)
);


ALTER TABLE public.users OWNER TO smartlearn_iaqb_user;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: smartlearn_iaqb_user
--

CREATE SEQUENCE public.users_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_id_seq OWNER TO smartlearn_iaqb_user;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: smartlearn_iaqb_user
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: admins id; Type: DEFAULT; Schema: public; Owner: smartlearn_iaqb_user
--

ALTER TABLE ONLY public.admins ALTER COLUMN id SET DEFAULT nextval('public.admins_id_seq'::regclass);


--
-- Name: messages id; Type: DEFAULT; Schema: public; Owner: smartlearn_iaqb_user
--

ALTER TABLE ONLY public.messages ALTER COLUMN id SET DEFAULT nextval('public.messages_id_seq'::regclass);


--
-- Name: questions id; Type: DEFAULT; Schema: public; Owner: smartlearn_iaqb_user
--

ALTER TABLE ONLY public.questions ALTER COLUMN id SET DEFAULT nextval('public.questions_id_seq'::regclass);


--
-- Name: students id; Type: DEFAULT; Schema: public; Owner: smartlearn_iaqb_user
--

ALTER TABLE ONLY public.students ALTER COLUMN id SET DEFAULT nextval('public.students_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: smartlearn_iaqb_user
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Data for Name: admins; Type: TABLE DATA; Schema: public; Owner: smartlearn_iaqb_user
--

COPY public.admins (id, username, password, created_at) FROM stdin;
1	rian	$2y$10$F4oXqSuvJrWmYUw5hHvZuOzh.sQju.aQQTYFZN79xY2dEx3vOevXW	2025-07-12 13:16:45.058186
\.


--
-- Data for Name: messages; Type: TABLE DATA; Schema: public; Owner: smartlearn_iaqb_user
--

COPY public.messages (id, task_id, sender_role, message, sent_at, type, file_path, seen_by_admin, seen_by_student, sender_id, sender_name) FROM stdin;
33	15	student	yyy	2025-07-14 14:48:35.392876	Other		t	f	7	Student
34	15	admin	good	2025-07-14 14:49:29.402373	Other		f	f	1	Admin
35	15	admin	view updated img	2025-07-14 14:50:31.108942	Other	uploads/chat/6875193717393_zero gpt report.docx	f	f	1	Admin
36	15	admin	bro	2025-07-14 14:51:08.844762	Other	uploads/chat/6875195cce2fe_Writer4.com	f	f	1	Admin
37	15	admin	hellot	2025-07-14 14:51:23.947169	Other		f	f	1	Admin
38	15	admin	hl	2025-07-14 18:16:18.402756	Other		f	f	1	Admin
39	15	admin	h	2025-07-14 18:16:42.075794	Other		f	f	1	Admin
40	16	student	hi	2025-07-14 19:31:09.126193	Other		t	f	7	Student
41	16	student	m	2025-07-14 19:36:29.045155	Other		t	f	7	Student
42	16	admin	kk	2025-07-14 19:37:44.557359	Other		f	f	1	Admin
43	16	admin	hh	2025-07-14 19:37:56.312174	Other		f	f	1	Admin
44	16	admin	lll	2025-07-14 19:38:17.951905	Other		f	f	1	Admin
45	16	admin	ii	2025-07-14 19:38:38.004077	Other		f	f	1	Admin
46	16	admin	ooo	2025-07-14 19:38:53.127527	Other		f	f	1	Admin
47	16	admin	qq	2025-07-14 19:39:04.93127	Other		f	f	1	Admin
1	1	student	hello	2025-07-12 10:47:00.584062	Other	\N	f	f	student_legacy	Student
2	2	student	good morning	2025-07-12 10:57:26.516574	Other	\N	f	f	student_legacy	Student
3	2	admin	hows that?	2025-07-12 11:35:15.64711	Other	\N	f	f	admin_legacy	Administrator
4	3	student	i wanna update something	2025-07-12 11:38:03.96309	Other	\N	f	f	student_legacy	Student
5	3	student	what do you wanna	2025-07-12 11:40:07.906681	Other	\N	f	f	student_legacy	Student
6	4	student	essay update	2025-07-12 11:44:07.00589	Other	\N	f	f	student_legacy	Student
7	4	student	yes	2025-07-12 11:44:43.412851	Other	\N	f	f	student_legacy	Student
8	2	student	good	2025-07-12 11:45:20.831441	Other	\N	f	f	student_legacy	Student
9	2	student	yesssdfghjjkkl;;	2025-07-12 11:46:39.26995	Other	\N	f	f	student_legacy	Student
10	2	student	;lkjhgfdxzs	2025-07-12 11:47:10.675686	Other	\N	f	f	student_legacy	Student
11	4	admin	GOOD	2025-07-12 13:17:25.617958	Other	\N	f	f	admin_legacy	Administrator
12	1	admin	WHATSUP	2025-07-12 13:19:32.446634	Other	\N	f	f	admin_legacy	Administrator
13	5	student	Hello Mr admin	2025-07-12 14:50:26.990803	Other	\N	f	f	student_legacy	Student
14	5	student	Yes my student	2025-07-12 14:51:56.33704	Other	\N	f	f	student_legacy	Student
15	5	admin	Hello	2025-07-12 15:08:16.381463	Other	\N	f	f	admin_legacy	Administrator
20	7	student	hello how is the progress	2025-07-12 17:51:54.893819	Other	\N	f	f	student_legacy	Student
21	7	student	heloooooooo	2025-07-12 17:56:51.293097	Other	\N	f	f	student_legacy	Student
22	8	student	hrrrrr	2025-07-12 17:57:16.910863	Other	\N	f	f	student_legacy	Student
23	7	student	hello	2025-07-12 17:57:53.637358	Other	\N	f	f	student_legacy	Student
24	8	admin	yyyy	2025-07-12 18:44:13.84639	Other		f	f	admin_legacy	Administrator
25	10	student	urgently needed	2025-07-12 19:04:33.92943	Other	uploads/chat/6872b1c1e1aaf_zero gpt report.docx,uploads/chat/6872b1c1e1e63_Writer4.com,uploads/chat/6872b1c1e218e_writer5.jpeg,uploads/chat/6872b1c1e2522_writer3.jpeg	t	f	student_legacy	Student
26	10	student	noted	2025-07-12 19:06:24.977711	Other		t	f	student_legacy	Student
27	11	student	now is my work	2025-07-12 19:33:18.922196	Other		t	f	student_legacy	Student
28	11	student	i get	2025-07-12 19:33:54.930993	Other		t	f	student_legacy	Student
29	12	student	yttrsdf	2025-07-12 21:24:45.068955	Other		f	f	student_legacy	Student
18	6	admin	hello	2025-07-12 16:58:27.55426	Other	\N	f	t	admin_legacy	Administrator
19	6	admin	hh	2025-07-12 16:58:38.337369	Other	\N	f	t	admin_legacy	Administrator
30	6	admin	hello	2025-07-13 19:07:35.651781	Other		f	f	admin_legacy	Administrator
16	6	student	Hello ajx	2025-07-12 15:11:49.853941	Other	\N	t	f	student_legacy	Student
17	6	student	Hello	2025-07-12 15:12:15.253175	Other	\N	t	f	student_legacy	Student
31	13	student	hi bro	2025-07-14 07:35:39.429402	Other		t	f	student_legacy	Student
32	13	student	yes	2025-07-14 07:36:23.700072	Other		t	f	student_legacy	Student
\.


--
-- Data for Name: questions; Type: TABLE DATA; Schema: public; Owner: smartlearn_iaqb_user
--

COPY public.questions (id, student_id, title, pages, description, other_info, file_path, created_at, price, student_name, question_text) FROM stdin;
1	1	tr	7	awesrdtfyguhijo	sedr	uploads/68723ce7e3cbb_Writer4.com	2025-07-12 10:45:59.940373	1.00	\N	\N
2	1	now	8	cgvh	zsdxf		2025-07-12 10:56:53.149966	1.00	\N	\N
3	4	algebra	18	find value of x		uploads/687248fb7ce61_Screenshot from 2025-07-11 08-17-11.png	2025-07-12 11:37:31.515353	1.00	\N	\N
4	1	essay	78	write essay on global warming		uploads/68724a63aae14_Writer4.com	2025-07-12 11:43:31.704088	1.00	\N	\N
5	1	Me	2	Me	Me	uploads/6872762059ae5_IMG-20250712-WA0008.jpg	2025-07-12 14:50:08.370364	1.00	\N	\N
6	1	Tr	5	AJ tr		uploads/68727b1695aee_IMG-20250712-WA0008.jpg	2025-07-12 15:11:18.614481	1.00	\N	\N
7	1	physics	2	whts physics			2025-07-12 17:51:11.147894	90.00	\N	\N
8	1	business	7	Great — your submit_question.php is nearly perfect and fully integrated with the new price field. Here's a quick summary and cleanup to finalize it:\r\n\r\n✅ Summary of Key Improvements Already in Place\r\n✔ Added price input field in the form (min="1", step="0.01")\r\n\r\n✔ File upload is optional\r\n\r\n✔ Validations ensure price ≥ $1 and pages ≥ 1\r\n\r\n✔ Uses NOW() for created_at\r\n\r\n✔ Displays submitted tasks with price and file download\r\n\r\n✔ Uses prepared statements for secure inserts\r\n\r\n✅ Small Final Enhancements\r\nYou can slightly clean up your form layout and formatting for readability. Here's the finalized, improved version (just copy-paste this):\r\n\r\n✅ ✅ FINAL submit_question.php\r\nphp\r\nCopy\r\nEdit\r\n<?php\r\nsession_start();\r\nrequire 'connect.php';\r\n\r\nif (!isset($_SESSION['student_id'])) {\r\n    header("Location: login.php");\r\n    exit;\r\n}\r\n\r\n$studentId = $_SESSION['student_id'];\r\n$error = '';\r\n$success = '';\r\n\r\n// Handle form submission\r\nif ($_SERVER['REQUEST_METHOD'] === 'POST') {\r\n    $title = trim($_POST['title']);\r\n    $pages = (int)$_POST['pages'];\r\n    $price = (float)$_POST['price'];\r\n    $description = trim($_POST['description']);\r\n    $other_info = trim($_POST['other_info']);\r\n\r\n    if ($price < 1) {\r\n        $error = "⚠️ Price must be at least $1.";\r\n    } elseif ($pages < 1) {\r\n        $error = "⚠️ Number of pages must be at least 1.";\r\n    } else {\r\n        // Handle optional file upload\r\n        $filePath = '';\r\n        if (!empty($_FILES['file']['name'])) {\r\n            $uploadDir = 'uploads/';\r\n            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);\r\n            $fileName = uniqid() . '_' . basename($_FILES['file']['name']);\r\n            $targetPath = $uploadDir . $fileName;\r\n\r\n            if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {\r\n                $filePath = $targetPath;\r\n            } else {\r\n                $error = "⚠️ File upload failed.";\r\n            }\r\n        }\r\n\r\n        if (!$error) {\r\n            $stmt = $conn->prepare("\r\n                INSERT INTO questions (student_id, title, pages, price, description, other_info, file_path, created_at) \r\n                VALUES (:student_id, :title, :pages, :price, :description, :other_info, :file_path, NOW())\r\n            ");\r\n            $stmt->execute([\r\n                'student_id' => $studentId,\r\n                'title' => $title,\r\n                'pages' => $pages,\r\n                'price' => $price,\r\n                'description' => $description,\r\n                'other_info' => $other_info,\r\n                'file_path' => $filePath\r\n            ]);\r\n            $success = "✅ Task submitted successfully.";\r\n        }\r\n    }\r\n}\r\n\r\n// Fetch existing tasks for this student\r\n$stmt = $conn->prepare("SELECT * FROM questions WHERE student_id = :student_id ORDER BY created_at DESC");\r\n$stmt->execute(['student_id' => $studentId]);\r\n$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);\r\n?>\r\n<?php include 'header.php'; ?>\r\n\r\n<!DOCTYPE html>\r\n<html>\r\n<head>\r\n    <title>Submit Task</title>\r\n    <style>\r\n        body { font-family: Arial, sans-serif; background: #f0f4f8; padding: 20px; }\r\n        .container { max-width: 900px; margin: auto; }\r\n        h2 { margin-bottom: 20px; text-align: center; }\r\n\r\n        form { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px #ccc; }\r\n        input, textarea, button {\r\n            width: 100%;\r\n            margin-top: 10px;\r\n            padding: 10px;\r\n            border-radius: 5px;\r\n            border: 1px solid #ccc;\r\n        }\r\n        button { background: black; color: white; border: none; }\r\n\r\n        .status { margin-top: 10px; color: green; }\r\n        .error { margin-top: 10px; color: red; }\r\n\r\n        table {\r\n            margin-top: 30px;\r\n            width: 100%;\r\n            border-collapse: collapse;\r\n            background: white;\r\n            box-shadow: 0 0 5px #ccc;\r\n        }\r\n        th, td {\r\n            padding: 12px;\r\n            border: 1px solid #ddd;\r\n        }\r\n        th { background: black; color: white; }\r\n        tr:hover { background: #f9f9f9; }\r\n        a.chat-link { background: #007BFF; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none; }\r\n    </style>\r\n</head>\r\n<body>\r\n<div class="container">\r\n    <h2>Submit a New Task</h2>\r\n\r\n    <?php if ($success): ?><div class="status"><?= $success ?></div><?php endif; ?>\r\n    <?php if ($error): ?><div class="error"><?= $error ?></div><?php endif; ?>\r\n\r\n    <form method="post" enctype="multipart/form-data">\r\n        <input type="text" name="title" placeholder="Title of the Task" required>\r\n        <input type="number" name="pages" placeholder="Number of Pages" required min="1">\r\n        <input type="number" name="price" placeholder="Your Budget in $" required min="1" step="0.01">\r\n        <textarea name="description" placeholder="Task Description" rows="4" required></textarea>\r\n        <textarea name="other_info" placeholder="Other Instructions (optional)" rows="3"></textarea>\r\n        <input type="file" name="file">\r\n        <button type="submit">Submit Task</button>\r\n    </form>\r\n\r\n    <?php if (!empty($tasks)): ?>\r\n        <h2>Your Submitted Tasks</h2>\r\n        <table>\r\n            <tr>\r\n                <th>Title</th>\r\n                <th>Pages</th>\r\n                <th>Price ($)</th>\r\n                <th>Description</th>\r\n                <th>Other Info</th>\r\n                <th>File</th>\r\n                <th>Posted</th>\r\n                <th>Chat</th>\r\n            </tr>\r\n            <?php foreach ($tasks as $task): ?>\r\n                <tr>\r\n                    <td><?= htmlspecialchars($task['title']) ?></td>\r\n                    <td><?= $task['pages'] ?></td>\r\n                    <td><?= number_format($task['price'], 2) ?></td>\r\n                    <td><?= nl2br(htmlspecialchars($task['description'])) ?></td>\r\n                    <td><?= nl2br(htmlspecialchars($task['other_info'])) ?></td>\r\n                    <td>\r\n                        <?php if ($task['file_path']): ?>\r\n                            <a href="<?= $task['file_path'] ?>" download>Download</a>\r\n                        <?php else: ?>\r\n                            N/A\r\n                        <?php endif; ?>\r\n                    </td>\r\n                    <td><?= $task['created_at'] ?></td>\r\n                    <td><a class="chat-link" href="chat.php?task_id=<?= $task['id'] ?>">Chat</a></td>\r\n                </tr>\r\n            <?php endforeach; ?>\r\n        </table>\r\n    <?php else: ?>\r\n        <p style="margin-top:20px;">You haven't submitted any tasks yet.</p>\r\n    <?php endif; ?>\r\n</div>\r\n</body>\r\n</html>\r\n<?php include 'footer.php'; ?>\r\nLet me know if you want:\r\n\r\n✅ Email confirmation after submission\r\n\r\n✅ Admin side view of all tasks\r\n\r\n✅ Live status ("open", "assigned", "completed")\r\n\r\n✅ Payment button (M-Pesa or PayPal)\r\n\r\nI'm ready to help you scale it.\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\nYou said:\r\n<?php\r\nsession_start();\r\n\r\n// Redirect to login page if not logged in\r\nif (!isset($_SESSION['student_id'])) {\r\n    header("Location: login.php");\r\n    exit();\r\n}\r\n?>\r\n\r\n<?php include 'header.php'; ?>\r\n\r\n<section class="hero">\r\n  <div class="carousel">\r\n    <img src="img/student1.jpg" class="runner layer1">\r\n    <img src="img/student2.jpg" class="runner layer2">\r\n    <img src="img/student3.jpg" class="runner layer3">\r\n  </div>\r\n  <div class="cta">\r\n    <h1>Join Thousands of Students Getting Help Today!</h1>\r\n    <a href="post_question.php" class="post-btn"><span class="btn-text">Post Your Question</span></a>\r\n  </div>\r\n</section>\r\n\r\n<?php include 'footer.php'; ?> this page is very boring add content that's fascinating for front end playing with CSS and also playa with colours index to fill the so much white space,,<?php\r\nsession_start();\r\n\r\n// Redirect to login page if not logged in\r\nif (!isset($_SESSION['student_id'])) {\r\n    header("Location: login.php");\r\n    exit();\r\n}\r\n?>\r\n\r\n<?php include 'header.php'; ?>\r\n\r\n<section class="hero">\r\n  <div class="carousel">\r\n    <img src="img/student1.jpg" class="runner layer1">\r\n    <img src="img/student2.jpg" class="runner layer2">\r\n    <img src="img/student3.jpg" class="runner layer3">\r\n  </div>\r\n  <div class="cta">\r\n    <h1>Join Thousands of Students Getting Help Today!</h1>\r\n    <a href="post_question.php" class="post-btn"><span class="btn-text">Post Your Question</span></a>\r\n  </div>\r\n</section>\r\n\r\n<?php include 'footer.php'; ?>\r\n\r\n\r\nChatGPT said:\r\nLet’s turn your boring homepage into a vibrant, modern student dashboard that feels alive. Here's what I’ve added:\r\n\r\n✅ Enhanced carousel with fading animation\r\n✅ Bold CTA with glowing button\r\n✅ Feature highlights section\r\n✅ Smooth color gradients and visual separation\r\n✅ Cleaner layout using Flexbox\r\n✅ Mobile responsiveness\r\n✅ Minimal white space\r\n\r\n✅ FULLY UPGRADED index.php\r\nphp\r\nCopy\r\nEdit\r\n<?php\r\nsession_start();\r\nif (!isset($_SESSION['student_id'])) {\r\n    header("Location: login.php");\r\n    exit();\r\n}\r\n?>\r\n\r\n<?php include 'header.php'; ?>\r\n\r\n<style>\r\n    body {\r\n        margin: 0;\r\n        font-family: 'Segoe UI', sans-serif;\r\n        background: linear-gradient(to right, #f3f4f6, #e0f7fa);\r\n    }\r\n\r\n    .hero {\r\n        position: relative;\r\n        text-align: center;\r\n        padding: 60px 20px;\r\n        color: white;\r\n        background: linear-gradient(to right, #004d7a, #008793, #00bf72, #a8eb12);\r\n        background-size: 400% 400%;\r\n        animation: gradientShift 10s ease infinite;\r\n    }\r\n\r\n    @keyframes gradientShift {\r\n        0% {background-position: 0% 50%;}\r\n        50% {background-position: 100% 50%;}\r\n        100% {background-position: 0% 50%;}\r\n    }\r\n\r\n    .carousel {\r\n        display: flex;\r\n        justify-content: center;\r\n        gap: 20px;\r\n        margin-bottom: 30px;\r\n        animation: fadeIn 2s ease;\r\n    }\r\n\r\n    .carousel img {\r\n        width: 250px;\r\n        height: 160px;\r\n        border-radius: 12px;\r\n        box-shadow: 0 8px 20px rgba(0,0,0,0.2);\r\n        transition: transform 0.3s ease;\r\n    }\r\n\r\n    .carousel img:hover {\r\n        transform: scale(1.05);\r\n    }\r\n\r\n    .cta h1 {\r\n        font-size: 2.5rem;\r\n        margin-bottom: 20px;\r\n        color: #fff;\r\n        text-shadow: 2px 2px 5px rgba(0,0,0,0.3);\r\n    }\r\n\r\n    .post-btn {\r\n        background-color: #ff1744;\r\n        color: white;\r\n        padding: 15px 30px;\r\n        font-size: 18px;\r\n        text-decoration: none;\r\n        border-radius: 8px;\r\n        box-shadow: 0 4px 10px rgba(0,0,0,0.3);\r\n        transition: background 0.3s ease, transform 0.3s ease;\r\n    }\r\n\r\n    .post-btn:hover {\r\n        background-color: #d50000;\r\n        transform: scale(1.05);\r\n    }\r\n\r\n    .features {\r\n        padding: 50px 20px;\r\n        background-color: #ffffff;\r\n        display: flex;\r\n        flex-wrap: wrap;\r\n        justify-content: center;\r\n        gap: 40px;\r\n    }\r\n\r\n    .feature-card {\r\n        background: #f9fafb;\r\n        padding: 25px;\r\n        border-radius: 12px;\r\n        max-width: 300px;\r\n        box-shadow: 0 6px 18px rgba(0,0,0,0.1);\r\n        text-align: center;\r\n        transition: transform 0.3s ease;\r\n    }\r\n\r\n    .feature-card:hover {\r\n        transform: translateY(-5px);\r\n    }\r\n\r\n    .feature-card h3 {\r\n        color: #333;\r\n        margin-bottom: 15px;\r\n    }\r\n\r\n    .feature-card p {\r\n        color: #555;\r\n        font-size: 15px;\r\n    }\r\n\r\n    @media (max-width: 768px) {\r\n        .carousel {\r\n            flex-direction: column;\r\n            align-items: center;\r\n        }\r\n    }\r\n\r\n    @keyframes fadeIn {\r\n        from {opacity: 0;}\r\n        to {opacity: 1;}\r\n    }\r\n</style>\r\n\r\n<section class="hero">\r\n    <div class="carousel">\r\n        <img src="img/student1.jpg" alt="Student 1">\r\n        <img src="img/student2.jpg" alt="Student 2">\r\n        <img src="img/student3.jpg" alt="Student 3">\r\n    </div>\r\n    <div class="cta">\r\n        <h1>Join Thousands of Students Getting Help Today!</h1>\r\n        <a href="post_question.php" class="post-btn"><span class="btn-text">Post Your Question</span></a>\r\n    </div>\r\n</section>\r\n\r\n<section class="features">\r\n    <div class="feature-card">\r\n        <h3>📚 Wide Range of Subjects</h3>\r\n        <p>Get help in math, science, writing, coding, and more — from qualified tutors worldwide.</p>\r\n    </div>\r\n    <div class="feature-card">\r\n        <h3>⏰ Fast Turnaround</h3>\r\n        <p>Set your deadline and get answers when you need them — even in a few hours.</p>\r\n    </div>\r\n    <div class="feature-card">\r\n        <h3>💬 Real-Time Support</h3>\r\n        <p>Live chat with your tutor, share files, and ask follow-ups instantly through our platform.</p>\r\n    </div>\r\n    <div class="feature-card">\r\n        <h3>💵 Flexible Pricing</h3>\r\n        <p>Pay based on your budget starting from just $1. Fair and transparent pricing always.</p>\r\n    </div>\r\n</section>\r\n\r\n<?php include 'footer.php'; ?>\r\n🔥 What’s Next?\r\nWant to make it even fancier?\r\n\r\n💡 Add testimonials from h	Great — your submit_question.php is nearly perfect and fully integrated with the new price field. Here's a quick summary and cleanup to finalize it:\r\n\r\n✅ Summary of Key Improvements Already in Place\r\n✔ Added price input field in the form (min="1", step="0.01")\r\n\r\n✔ File upload is optional\r\n\r\n✔ Validations ensure price ≥ $1 and pages ≥ 1\r\n\r\n✔ Uses NOW() for created_at\r\n\r\n✔ Displays submitted tasks with price and file download\r\n\r\n✔ Uses prepared statements for secure inserts\r\n\r\n✅ Small Final Enhancements\r\nYou can slightly clean up your form layout and formatting for readability. Here's the finalized, improved version (just copy-paste this):\r\n\r\n✅ ✅ FINAL submit_question.php\r\nphp\r\nCopy\r\nEdit\r\n<?php\r\nsession_start();\r\nrequire 'connect.php';\r\n\r\nif (!isset($_SESSION['student_id'])) {\r\n    header("Location: login.php");\r\n    exit;\r\n}\r\n\r\n$studentId = $_SESSION['student_id'];\r\n$error = '';\r\n$success = '';\r\n\r\n// Handle form submission\r\nif ($_SERVER['REQUEST_METHOD'] === 'POST') {\r\n    $title = trim($_POST['title']);\r\n    $pages = (int)$_POST['pages'];\r\n    $price = (float)$_POST['price'];\r\n    $description = trim($_POST['description']);\r\n    $other_info = trim($_POST['other_info']);\r\n\r\n    if ($price < 1) {\r\n        $error = "⚠️ Price must be at least $1.";\r\n    } elseif ($pages < 1) {\r\n        $error = "⚠️ Number of pages must be at least 1.";\r\n    } else {\r\n        // Handle optional file upload\r\n        $filePath = '';\r\n        if (!empty($_FILES['file']['name'])) {\r\n            $uploadDir = 'uploads/';\r\n            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);\r\n            $fileName = uniqid() . '_' . basename($_FILES['file']['name']);\r\n            $targetPath = $uploadDir . $fileName;\r\n\r\n            if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {\r\n                $filePath = $targetPath;\r\n            } else {\r\n                $error = "⚠️ File upload failed.";\r\n            }\r\n        }\r\n\r\n        if (!$error) {\r\n            $stmt = $conn->prepare("\r\n                INSERT INTO questions (student_id, title, pages, price, description, other_info, file_path, created_at) \r\n                VALUES (:student_id, :title, :pages, :price, :description, :other_info, :file_path, NOW())\r\n            ");\r\n            $stmt->execute([\r\n                'student_id' => $studentId,\r\n                'title' => $title,\r\n                'pages' => $pages,\r\n                'price' => $price,\r\n                'description' => $description,\r\n                'other_info' => $other_info,\r\n                'file_path' => $filePath\r\n            ]);\r\n            $success = "✅ Task submitted successfully.";\r\n        }\r\n    }\r\n}\r\n\r\n// Fetch existing tasks for this student\r\n$stmt = $conn->prepare("SELECT * FROM questions WHERE student_id = :student_id ORDER BY created_at DESC");\r\n$stmt->execute(['student_id' => $studentId]);\r\n$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);\r\n?>\r\n<?php include 'header.php'; ?>\r\n\r\n<!DOCTYPE html>\r\n<html>\r\n<head>\r\n    <title>Submit Task</title>\r\n    <style>\r\n        body { font-family: Arial, sans-serif; background: #f0f4f8; padding: 20px; }\r\n        .container { max-width: 900px; margin: auto; }\r\n        h2 { margin-bottom: 20px; text-align: center; }\r\n\r\n        form { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px #ccc; }\r\n        input, textarea, button {\r\n            width: 100%;\r\n            margin-top: 10px;\r\n            padding: 10px;\r\n            border-radius: 5px;\r\n            border: 1px solid #ccc;\r\n        }\r\n        button { background: black; color: white; border: none; }\r\n\r\n        .status { margin-top: 10px; color: green; }\r\n        .error { margin-top: 10px; color: red; }\r\n\r\n        table {\r\n            margin-top: 30px;\r\n            width: 100%;\r\n            border-collapse: collapse;\r\n            background: white;\r\n            box-shadow: 0 0 5px #ccc;\r\n        }\r\n        th, td {\r\n            padding: 12px;\r\n            border: 1px solid #ddd;\r\n        }\r\n        th { background: black; color: white; }\r\n        tr:hover { background: #f9f9f9; }\r\n        a.chat-link { background: #007BFF; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none; }\r\n    </style>\r\n</head>\r\n<body>\r\n<div class="container">\r\n    <h2>Submit a New Task</h2>\r\n\r\n    <?php if ($success): ?><div class="status"><?= $success ?></div><?php endif; ?>\r\n    <?php if ($error): ?><div class="error"><?= $error ?></div><?php endif; ?>\r\n\r\n    <form method="post" enctype="multipart/form-data">\r\n        <input type="text" name="title" placeholder="Title of the Task" required>\r\n        <input type="number" name="pages" placeholder="Number of Pages" required min="1">\r\n        <input type="number" name="price" placeholder="Your Budget in $" required min="1" step="0.01">\r\n        <textarea name="description" placeholder="Task Description" rows="4" required></textarea>\r\n        <textarea name="other_info" placeholder="Other Instructions (optional)" rows="3"></textarea>\r\n        <input type="file" name="file">\r\n        <button type="submit">Submit Task</button>\r\n    </form>\r\n\r\n    <?php if (!empty($tasks)): ?>\r\n        <h2>Your Submitted Tasks</h2>\r\n        <table>\r\n            <tr>\r\n                <th>Title</th>\r\n                <th>Pages</th>\r\n                <th>Price ($)</th>\r\n                <th>Description</th>\r\n                <th>Other Info</th>\r\n                <th>File</th>\r\n                <th>Posted</th>\r\n                <th>Chat</th>\r\n            </tr>\r\n            <?php foreach ($tasks as $task): ?>\r\n                <tr>\r\n                    <td><?= htmlspecialchars($task['title']) ?></td>\r\n                    <td><?= $task['pages'] ?></td>\r\n                    <td><?= number_format($task['price'], 2) ?></td>\r\n                    <td><?= nl2br(htmlspecialchars($task['description'])) ?></td>\r\n                    <td><?= nl2br(htmlspecialchars($task['other_info'])) ?></td>\r\n                    <td>\r\n                        <?php if ($task['file_path']): ?>\r\n                            <a href="<?= $task['file_path'] ?>" download>Download</a>\r\n                        <?php else: ?>\r\n                            N/A\r\n                        <?php endif; ?>\r\n                    </td>\r\n                    <td><?= $task['created_at'] ?></td>\r\n                    <td><a class="chat-link" href="chat.php?task_id=<?= $task['id'] ?>">Chat</a></td>\r\n                </tr>\r\n            <?php endforeach; ?>\r\n        </table>\r\n    <?php else: ?>\r\n        <p style="margin-top:20px;">You haven't submitted any tasks yet.</p>\r\n    <?php endif; ?>\r\n</div>\r\n</body>\r\n</html>\r\n<?php include 'footer.php'; ?>\r\nLet me know if you want:\r\n\r\n✅ Email confirmation after submission\r\n\r\n✅ Admin side view of all tasks\r\n\r\n✅ Live status ("open", "assigned", "completed")\r\n\r\n✅ Payment button (M-Pesa or PayPal)\r\n\r\nI'm ready to help you scale it.\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\nYou said:\r\n<?php\r\nsession_start();\r\n\r\n// Redirect to login page if not logged in\r\nif (!isset($_SESSION['student_id'])) {\r\n    header("Location: login.php");\r\n    exit();\r\n}\r\n?>\r\n\r\n<?php include 'header.php'; ?>\r\n\r\n<section class="hero">\r\n  <div class="carousel">\r\n    <img src="img/student1.jpg" class="runner layer1">\r\n    <img src="img/student2.jpg" class="runner layer2">\r\n    <img src="img/student3.jpg" class="runner layer3">\r\n  </div>\r\n  <div class="cta">\r\n    <h1>Join Thousands of Students Getting Help Today!</h1>\r\n    <a href="post_question.php" class="post-btn"><span class="btn-text">Post Your Question</span></a>\r\n  </div>\r\n</section>\r\n\r\n<?php include 'footer.php'; ?> this page is very boring add content that's fascinating for front end playing with CSS and also playa with colours index to fill the so much white space,,<?php\r\nsession_start();\r\n\r\n// Redirect to login page if not logged in\r\nif (!isset($_SESSION['student_id'])) {\r\n    header("Location: login.php");\r\n    exit();\r\n}\r\n?>\r\n\r\n<?php include 'header.php'; ?>\r\n\r\n<section class="hero">\r\n  <div class="carousel">\r\n    <img src="img/student1.jpg" class="runner layer1">\r\n    <img src="img/student2.jpg" class="runner layer2">\r\n    <img src="img/student3.jpg" class="runner layer3">\r\n  </div>\r\n  <div class="cta">\r\n    <h1>Join Thousands of Students Getting Help Today!</h1>\r\n    <a href="post_question.php" class="post-btn"><span class="btn-text">Post Your Question</span></a>\r\n  </div>\r\n</section>\r\n\r\n<?php include 'footer.php'; ?>\r\n\r\n\r\nChatGPT said:\r\nLet’s turn your boring homepage into a vibrant, modern student dashboard that feels alive. Here's what I’ve added:\r\n\r\n✅ Enhanced carousel with fading animation\r\n✅ Bold CTA with glowing button\r\n✅ Feature highlights section\r\n✅ Smooth color gradients and visual separation\r\n✅ Cleaner layout using Flexbox\r\n✅ Mobile responsiveness\r\n✅ Minimal white space\r\n\r\n✅ FULLY UPGRADED index.php\r\nphp\r\nCopy\r\nEdit\r\n<?php\r\nsession_start();\r\nif (!isset($_SESSION['student_id'])) {\r\n    header("Location: login.php");\r\n    exit();\r\n}\r\n?>\r\n\r\n<?php include 'header.php'; ?>\r\n\r\n<style>\r\n    body {\r\n        margin: 0;\r\n        font-family: 'Segoe UI', sans-serif;\r\n        background: linear-gradient(to right, #f3f4f6, #e0f7fa);\r\n    }\r\n\r\n    .hero {\r\n        position: relative;\r\n        text-align: center;\r\n        padding: 60px 20px;\r\n        color: white;\r\n        background: linear-gradient(to right, #004d7a, #008793, #00bf72, #a8eb12);\r\n        background-size: 400% 400%;\r\n        animation: gradientShift 10s ease infinite;\r\n    }\r\n\r\n    @keyframes gradientShift {\r\n        0% {background-position: 0% 50%;}\r\n        50% {background-position: 100% 50%;}\r\n        100% {background-position: 0% 50%;}\r\n    }\r\n\r\n    .carousel {\r\n        display: flex;\r\n        justify-content: center;\r\n        gap: 20px;\r\n        margin-bottom: 30px;\r\n        animation: fadeIn 2s ease;\r\n    }\r\n\r\n    .carousel img {\r\n        width: 250px;\r\n        height: 160px;\r\n        border-radius: 12px;\r\n        box-shadow: 0 8px 20px rgba(0,0,0,0.2);\r\n        transition: transform 0.3s ease;\r\n    }\r\n\r\n    .carousel img:hover {\r\n        transform: scale(1.05);\r\n    }\r\n\r\n    .cta h1 {\r\n        font-size: 2.5rem;\r\n        margin-bottom: 20px;\r\n        color: #fff;\r\n        text-shadow: 2px 2px 5px rgba(0,0,0,0.3);\r\n    }\r\n\r\n    .post-btn {\r\n        background-color: #ff1744;\r\n        color: white;\r\n        padding: 15px 30px;\r\n        font-size: 18px;\r\n        text-decoration: none;\r\n        border-radius: 8px;\r\n        box-shadow: 0 4px 10px rgba(0,0,0,0.3);\r\n        transition: background 0.3s ease, transform 0.3s ease;\r\n    }\r\n\r\n    .post-btn:hover {\r\n        background-color: #d50000;\r\n        transform: scale(1.05);\r\n    }\r\n\r\n    .features {\r\n        padding: 50px 20px;\r\n        background-color: #ffffff;\r\n        display: flex;\r\n        flex-wrap: wrap;\r\n        justify-content: center;\r\n        gap: 40px;\r\n    }\r\n\r\n    .feature-card {\r\n        background: #f9fafb;\r\n        padding: 25px;\r\n        border-radius: 12px;\r\n        max-width: 300px;\r\n        box-shadow: 0 6px 18px rgba(0,0,0,0.1);\r\n        text-align: center;\r\n        transition: transform 0.3s ease;\r\n    }\r\n\r\n    .feature-card:hover {\r\n        transform: translateY(-5px);\r\n    }\r\n\r\n    .feature-card h3 {\r\n        color: #333;\r\n        margin-bottom: 15px;\r\n    }\r\n\r\n    .feature-card p {\r\n        color: #555;\r\n        font-size: 15px;\r\n    }\r\n\r\n    @media (max-width: 768px) {\r\n        .carousel {\r\n            flex-direction: column;\r\n            align-items: center;\r\n        }\r\n    }\r\n\r\n    @keyframes fadeIn {\r\n        from {opacity: 0;}\r\n        to {opacity: 1;}\r\n    }\r\n</style>\r\n\r\n<section class="hero">\r\n    <div class="carousel">\r\n        <img src="img/student1.jpg" alt="Student 1">\r\n        <img src="img/student2.jpg" alt="Student 2">\r\n        <img src="img/student3.jpg" alt="Student 3">\r\n    </div>\r\n    <div class="cta">\r\n        <h1>Join Thousands of Students Getting Help Today!</h1>\r\n        <a href="post_question.php" class="post-btn"><span class="btn-text">Post Your Question</span></a>\r\n    </div>\r\n</section>\r\n\r\n<section class="features">\r\n    <div class="feature-card">\r\n        <h3>📚 Wide Range of Subjects</h3>\r\n        <p>Get help in math, science, writing, coding, and more — from qualified tutors worldwide.</p>\r\n    </div>\r\n    <div class="feature-card">\r\n        <h3>⏰ Fast Turnaround</h3>\r\n        <p>Set your deadline and get answers when you need them — even in a few hours.</p>\r\n    </div>\r\n    <div class="feature-card">\r\n        <h3>💬 Real-Time Support</h3>\r\n        <p>Live chat with your tutor, share files, and ask follow-ups instantly through our platform.</p>\r\n    </div>\r\n    <div class="feature-card">\r\n        <h3>💵 Flexible Pricing</h3>\r\n        <p>Pay based on your budget starting from just $1. Fair and transparent pricing always.</p>\r\n    </div>\r\n</section>\r\n\r\n<?php include 'footer.php'; ?>\r\n🔥 What’s Next?\r\nWant to make it even fancier?\r\n\r\n💡 Add testimonials from h		2025-07-12 17:55:19.75607	6.00	\N	\N
9	1	tr	4	ttrrrrrrrew			2025-07-12 18:45:08.982605	4.00	\N	\N
10	1	you	4	r	r	uploads/6872b17006bb9_writer2.jpeg	2025-07-12 19:03:12.030881	9.00	\N	\N
11	1	now	8	des			2025-07-12 19:32:42.907802	6.00	\N	\N
12	1	uuu	8	fghj			2025-07-12 20:37:10.892212	8.00	\N	\N
13	6	maths	3	find y			2025-07-14 07:34:52.394356	3.00	\N	\N
14	7	iii	8	jjkl;kjjh			2025-07-14 08:15:45.77908	8.00	\N	\N
15	7	lkjhgfd	7	serdtrfyg			2025-07-14 14:48:07.196391	7.00	\N	\N
16	7	uu	9	gy			2025-07-14 19:19:15.044374	9.00	\N	\N
\.


--
-- Data for Name: students; Type: TABLE DATA; Schema: public; Owner: smartlearn_iaqb_user
--

COPY public.students (id, name, email, created_at) FROM stdin;
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: smartlearn_iaqb_user
--

COPY public.users (id, full_name, email, password, created_at, name) FROM stdin;
1	brian	brian@gmail.com	$2y$10$n9bYxTdColQHTWxIwWQ91uikEB5ZR.DyqkEETw.E40Zr7TCpBA6eu	2025-07-11 20:52:35.262148	Student
4	beatrice	beatrice@gmail.com	$2y$10$XO9MMgkXocgptX0GcpfxH.IUgPwQ3tIfrYc5ba0oP9BnymG0Q3X0S	2025-07-12 07:09:33.101964	Student
5	mary	mary@gmail.com	$2y$10$Y1xLCX/Kb2ZWTBCSKTSQpOiG.XzY4BpWDarmXHx/ve9Ja78Ti0ZqG	2025-07-12 07:17:13.960549	Student
6	rianah	rianah@gmail.com	$2y$10$CFspbs6hnv8eyvHdF7XvWelanftv0j9k9aTaH5uqegG7lO33HFQoa	2025-07-14 07:33:28.231441	Student
7	brianah	brianah@gmail.com	$2y$10$vQ7bzpndHUISceSjsGWQYeRrDG510AW2ufAjdNJUCEsm0AAQ5VjKK	2025-07-14 08:14:47.136299	Student
\.


--
-- Name: admins_id_seq; Type: SEQUENCE SET; Schema: public; Owner: smartlearn_iaqb_user
--

SELECT pg_catalog.setval('public.admins_id_seq', 1, true);


--
-- Name: messages_id_seq; Type: SEQUENCE SET; Schema: public; Owner: smartlearn_iaqb_user
--

SELECT pg_catalog.setval('public.messages_id_seq', 47, true);


--
-- Name: questions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: smartlearn_iaqb_user
--

SELECT pg_catalog.setval('public.questions_id_seq', 16, true);


--
-- Name: students_id_seq; Type: SEQUENCE SET; Schema: public; Owner: smartlearn_iaqb_user
--

SELECT pg_catalog.setval('public.students_id_seq', 1, false);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: smartlearn_iaqb_user
--

SELECT pg_catalog.setval('public.users_id_seq', 7, true);


--
-- Name: admins admins_pkey; Type: CONSTRAINT; Schema: public; Owner: smartlearn_iaqb_user
--

ALTER TABLE ONLY public.admins
    ADD CONSTRAINT admins_pkey PRIMARY KEY (id);


--
-- Name: admins admins_username_key; Type: CONSTRAINT; Schema: public; Owner: smartlearn_iaqb_user
--

ALTER TABLE ONLY public.admins
    ADD CONSTRAINT admins_username_key UNIQUE (username);


--
-- Name: messages messages_pkey; Type: CONSTRAINT; Schema: public; Owner: smartlearn_iaqb_user
--

ALTER TABLE ONLY public.messages
    ADD CONSTRAINT messages_pkey PRIMARY KEY (id);


--
-- Name: questions questions_pkey; Type: CONSTRAINT; Schema: public; Owner: smartlearn_iaqb_user
--

ALTER TABLE ONLY public.questions
    ADD CONSTRAINT questions_pkey PRIMARY KEY (id);


--
-- Name: students students_email_key; Type: CONSTRAINT; Schema: public; Owner: smartlearn_iaqb_user
--

ALTER TABLE ONLY public.students
    ADD CONSTRAINT students_email_key UNIQUE (email);


--
-- Name: students students_pkey; Type: CONSTRAINT; Schema: public; Owner: smartlearn_iaqb_user
--

ALTER TABLE ONLY public.students
    ADD CONSTRAINT students_pkey PRIMARY KEY (id);


--
-- Name: users users_email_key; Type: CONSTRAINT; Schema: public; Owner: smartlearn_iaqb_user
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: smartlearn_iaqb_user
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: idx_messages_seen; Type: INDEX; Schema: public; Owner: smartlearn_iaqb_user
--

CREATE INDEX idx_messages_seen ON public.messages USING btree (task_id, seen_by_admin, seen_by_student);


--
-- Name: idx_messages_task_sender; Type: INDEX; Schema: public; Owner: smartlearn_iaqb_user
--

CREATE INDEX idx_messages_task_sender ON public.messages USING btree (task_id, sender_role);


--
-- Name: messages messages_task_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: smartlearn_iaqb_user
--

ALTER TABLE ONLY public.messages
    ADD CONSTRAINT messages_task_id_fkey FOREIGN KEY (task_id) REFERENCES public.questions(id);


--
-- Name: questions questions_student_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: smartlearn_iaqb_user
--

ALTER TABLE ONLY public.questions
    ADD CONSTRAINT questions_student_id_fkey FOREIGN KEY (student_id) REFERENCES public.users(id);


--
-- Name: DEFAULT PRIVILEGES FOR SEQUENCES; Type: DEFAULT ACL; Schema: -; Owner: postgres
--

ALTER DEFAULT PRIVILEGES FOR ROLE postgres GRANT ALL ON SEQUENCES TO smartlearn_iaqb_user;


--
-- Name: DEFAULT PRIVILEGES FOR TYPES; Type: DEFAULT ACL; Schema: -; Owner: postgres
--

ALTER DEFAULT PRIVILEGES FOR ROLE postgres GRANT ALL ON TYPES TO smartlearn_iaqb_user;


--
-- Name: DEFAULT PRIVILEGES FOR FUNCTIONS; Type: DEFAULT ACL; Schema: -; Owner: postgres
--

ALTER DEFAULT PRIVILEGES FOR ROLE postgres GRANT ALL ON FUNCTIONS TO smartlearn_iaqb_user;


--
-- Name: DEFAULT PRIVILEGES FOR TABLES; Type: DEFAULT ACL; Schema: -; Owner: postgres
--

ALTER DEFAULT PRIVILEGES FOR ROLE postgres GRANT ALL ON TABLES TO smartlearn_iaqb_user;


--
-- PostgreSQL database dump complete
--

