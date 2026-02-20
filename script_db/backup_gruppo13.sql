--
-- PostgreSQL database dump
--

\restrict pnQANTEHMdIEEFV0YDCu8kjH6s6vy17PFZkaC5tLzeGEkLg942BNAkiuJecdGVv

-- Dumped from database version 18.1
-- Dumped by pg_dump version 18.1

-- Started on 2026-02-20 19:44:35

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 226 (class 1259 OID 16803)
-- Name: camere; Type: TABLE; Schema: public; Owner: www
--

CREATE TABLE public.camere (
    id integer NOT NULL,
    titolo character varying(100) NOT NULL,
    descrizione text,
    prezzo numeric(10,2),
    galleria text NOT NULL
);


ALTER TABLE public.camere OWNER TO www;

--
-- TOC entry 225 (class 1259 OID 16802)
-- Name: camere_id_seq; Type: SEQUENCE; Schema: public; Owner: www
--

CREATE SEQUENCE public.camere_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.camere_id_seq OWNER TO www;

--
-- TOC entry 5057 (class 0 OID 0)
-- Dependencies: 225
-- Name: camere_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: www
--

ALTER SEQUENCE public.camere_id_seq OWNED BY public.camere.id;


--
-- TOC entry 228 (class 1259 OID 16841)
-- Name: contenuti; Type: TABLE; Schema: public; Owner: www
--

CREATE TABLE public.contenuti (
    id integer NOT NULL,
    titolo text NOT NULL,
    descrizione text NOT NULL,
    immagine text NOT NULL
);


ALTER TABLE public.contenuti OWNER TO www;

--
-- TOC entry 227 (class 1259 OID 16840)
-- Name: contenuti_id_seq; Type: SEQUENCE; Schema: public; Owner: www
--

CREATE SEQUENCE public.contenuti_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.contenuti_id_seq OWNER TO www;

--
-- TOC entry 5058 (class 0 OID 0)
-- Dependencies: 227
-- Name: contenuti_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: www
--

ALTER SEQUENCE public.contenuti_id_seq OWNED BY public.contenuti.id;


--
-- TOC entry 221 (class 1259 OID 16687)
-- Name: pacchetti_id_seq; Type: SEQUENCE; Schema: public; Owner: www
--

CREATE SEQUENCE public.pacchetti_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.pacchetti_id_seq OWNER TO www;

--
-- TOC entry 222 (class 1259 OID 16688)
-- Name: pacchetti; Type: TABLE; Schema: public; Owner: www
--

CREATE TABLE public.pacchetti (
    id integer DEFAULT nextval('public.pacchetti_id_seq'::regclass) NOT NULL,
    nome text NOT NULL,
    descrizione text NOT NULL,
    prezzo numeric(6,2),
    immagine text
);


ALTER TABLE public.pacchetti OWNER TO www;

--
-- TOC entry 224 (class 1259 OID 16728)
-- Name: prenotazioni; Type: TABLE; Schema: public; Owner: www
--

CREATE TABLE public.prenotazioni (
    id integer NOT NULL,
    id_utente integer,
    nome_pacchetto text NOT NULL,
    data_prenotazione date DEFAULT CURRENT_DATE,
    prezzo numeric(10,2) NOT NULL
);


ALTER TABLE public.prenotazioni OWNER TO www;

--
-- TOC entry 223 (class 1259 OID 16727)
-- Name: prenotazioni_id_seq; Type: SEQUENCE; Schema: public; Owner: www
--

CREATE SEQUENCE public.prenotazioni_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.prenotazioni_id_seq OWNER TO www;

--
-- TOC entry 5059 (class 0 OID 0)
-- Dependencies: 223
-- Name: prenotazioni_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: www
--

ALTER SEQUENCE public.prenotazioni_id_seq OWNED BY public.prenotazioni.id;


--
-- TOC entry 220 (class 1259 OID 16673)
-- Name: utenti; Type: TABLE; Schema: public; Owner: www
--

CREATE TABLE public.utenti (
    id integer NOT NULL,
    username text NOT NULL,
    email text CONSTRAINT utenti_password_not_null NOT NULL,
    password text CONSTRAINT utenti_password_not_null1 NOT NULL
);


ALTER TABLE public.utenti OWNER TO www;

--
-- TOC entry 219 (class 1259 OID 16672)
-- Name: utenti_id_seq; Type: SEQUENCE; Schema: public; Owner: www
--

CREATE SEQUENCE public.utenti_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.utenti_id_seq OWNER TO www;

--
-- TOC entry 5060 (class 0 OID 0)
-- Dependencies: 219
-- Name: utenti_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: www
--

ALTER SEQUENCE public.utenti_id_seq OWNED BY public.utenti.id;


--
-- TOC entry 4880 (class 2604 OID 16806)
-- Name: camere id; Type: DEFAULT; Schema: public; Owner: www
--

ALTER TABLE ONLY public.camere ALTER COLUMN id SET DEFAULT nextval('public.camere_id_seq'::regclass);


--
-- TOC entry 4881 (class 2604 OID 16844)
-- Name: contenuti id; Type: DEFAULT; Schema: public; Owner: www
--

ALTER TABLE ONLY public.contenuti ALTER COLUMN id SET DEFAULT nextval('public.contenuti_id_seq'::regclass);


--
-- TOC entry 4878 (class 2604 OID 16731)
-- Name: prenotazioni id; Type: DEFAULT; Schema: public; Owner: www
--

ALTER TABLE ONLY public.prenotazioni ALTER COLUMN id SET DEFAULT nextval('public.prenotazioni_id_seq'::regclass);


--
-- TOC entry 4876 (class 2604 OID 16676)
-- Name: utenti id; Type: DEFAULT; Schema: public; Owner: www
--

ALTER TABLE ONLY public.utenti ALTER COLUMN id SET DEFAULT nextval('public.utenti_id_seq'::regclass);


--
-- TOC entry 5049 (class 0 OID 16803)
-- Dependencies: 226
-- Data for Name: camere; Type: TABLE DATA; Schema: public; Owner: www
--

COPY public.camere (id, titolo, descrizione, prezzo, galleria) FROM stdin;
1	SUITE	Scopri la nostra Suite esclusiva con vasca idromassaggio, sauna e cucina moderna. Il massimo del relax	150.00	s1.jpeg,s2.jpeg,s3.jpeg,s4.jpeg,s5.jpeg,s6.jpeg,s7.jpeg,s8.jpeg
2	DELUXE	Vivi la tua esperienza Deluxe con vasca privata in camera, zona living per cene romantiche e cromoterapia.	100.00	s9.jpeg,s10.jpeg,s11.jpeg,s12.jpeg,s13.jpeg,s14.jpeg,s15.jpeg,s16.jpeg
\.


--
-- TOC entry 5051 (class 0 OID 16841)
-- Dependencies: 228
-- Data for Name: contenuti; Type: TABLE DATA; Schema: public; Owner: www
--

COPY public.contenuti (id, titolo, descrizione, immagine) FROM stdin;
1	Suite	Una casa vacanze spaziosa ed elegante, pensata per accogliere fino a quattro ospiti, dotata di sauna privata, \n    ampia vasca relax e cucina moderna completamente attrezzata, \n    ideale per chi desidera comfort, privacy e un’esperienza di soggiorno esclusiva.	suite1.jpeg
2	Deluxe	Un ambiente intimo e curato nei dettagli, perfetto per due persone, \n    con vasca relax e cucina moderna attrezzata, \n    ideale per una fuga romantica o un soggiorno all’insegna del benessere e della tranquillità.	deluxe.jpeg
3	Pacchetti	Pacchetti aperitivi romantici pensati per rendere il soggiorno ancora più speciale, \n    con allestimenti curati, atmosfera suggestiva e momenti di relax da condividere, \n    perfetti per celebrare occasioni uniche o semplicemente regalarsi un’esperienza indimenticabile.	ape3.jpeg
\.


--
-- TOC entry 5045 (class 0 OID 16688)
-- Dependencies: 222
-- Data for Name: pacchetti; Type: TABLE DATA; Schema: public; Owner: www
--

COPY public.pacchetti (id, nome, descrizione, prezzo, immagine) FROM stdin;
1	Pacchetto Base	Allestimento love standard per una serata romantica.	19.00	packbase.jpeg
2	Love Base	Allestimento + aperitivo con stuzzichini locali.	29.00	love-base.jpeg
3	Love Plus	Allestimento + aperitivo large con bottiglia di spumante.	39.00	ape3.jpeg
4	Love Sushi	Allestimento + box sushi speciale per due persone.	49.00	imgsushi.jpeg
\.


--
-- TOC entry 5047 (class 0 OID 16728)
-- Dependencies: 224
-- Data for Name: prenotazioni; Type: TABLE DATA; Schema: public; Owner: www
--

COPY public.prenotazioni (id, id_utente, nome_pacchetto, data_prenotazione, prezzo) FROM stdin;
38	19	SUITE	2026-02-20	150.00
\.


--
-- TOC entry 5043 (class 0 OID 16673)
-- Dependencies: 220
-- Data for Name: utenti; Type: TABLE DATA; Schema: public; Owner: www
--

COPY public.utenti (id, username, email, password) FROM stdin;
19	giulia	giu@gmail.com	$2y$10$2VhQkatH.WC/WvNwFQ0m5.lhqw2XT7zD2XHEYXYpS.B4vPiEsBaJC
\.


--
-- TOC entry 5061 (class 0 OID 0)
-- Dependencies: 225
-- Name: camere_id_seq; Type: SEQUENCE SET; Schema: public; Owner: www
--

SELECT pg_catalog.setval('public.camere_id_seq', 2, true);


--
-- TOC entry 5062 (class 0 OID 0)
-- Dependencies: 227
-- Name: contenuti_id_seq; Type: SEQUENCE SET; Schema: public; Owner: www
--

SELECT pg_catalog.setval('public.contenuti_id_seq', 3, true);


--
-- TOC entry 5063 (class 0 OID 0)
-- Dependencies: 221
-- Name: pacchetti_id_seq; Type: SEQUENCE SET; Schema: public; Owner: www
--

SELECT pg_catalog.setval('public.pacchetti_id_seq', 4, true);


--
-- TOC entry 5064 (class 0 OID 0)
-- Dependencies: 223
-- Name: prenotazioni_id_seq; Type: SEQUENCE SET; Schema: public; Owner: www
--

SELECT pg_catalog.setval('public.prenotazioni_id_seq', 38, true);


--
-- TOC entry 5065 (class 0 OID 0)
-- Dependencies: 219
-- Name: utenti_id_seq; Type: SEQUENCE SET; Schema: public; Owner: www
--

SELECT pg_catalog.setval('public.utenti_id_seq', 19, true);


--
-- TOC entry 4891 (class 2606 OID 16813)
-- Name: camere camere_pkey; Type: CONSTRAINT; Schema: public; Owner: www
--

ALTER TABLE ONLY public.camere
    ADD CONSTRAINT camere_pkey PRIMARY KEY (id);


--
-- TOC entry 4893 (class 2606 OID 16852)
-- Name: contenuti contenuti_pkey; Type: CONSTRAINT; Schema: public; Owner: www
--

ALTER TABLE ONLY public.contenuti
    ADD CONSTRAINT contenuti_pkey PRIMARY KEY (id);


--
-- TOC entry 4887 (class 2606 OID 16698)
-- Name: pacchetti pacchetti_pkey; Type: CONSTRAINT; Schema: public; Owner: www
--

ALTER TABLE ONLY public.pacchetti
    ADD CONSTRAINT pacchetti_pkey PRIMARY KEY (id);


--
-- TOC entry 4889 (class 2606 OID 16738)
-- Name: prenotazioni prenotazioni_pkey; Type: CONSTRAINT; Schema: public; Owner: www
--

ALTER TABLE ONLY public.prenotazioni
    ADD CONSTRAINT prenotazioni_pkey PRIMARY KEY (id);


--
-- TOC entry 4883 (class 2606 OID 16681)
-- Name: utenti utenti_pkey; Type: CONSTRAINT; Schema: public; Owner: www
--

ALTER TABLE ONLY public.utenti
    ADD CONSTRAINT utenti_pkey PRIMARY KEY (id);


--
-- TOC entry 4885 (class 2606 OID 16718)
-- Name: utenti utenti_username_key; Type: CONSTRAINT; Schema: public; Owner: www
--

ALTER TABLE ONLY public.utenti
    ADD CONSTRAINT utenti_username_key UNIQUE (username);


--
-- TOC entry 4894 (class 2606 OID 16739)
-- Name: prenotazioni prenotazioni_id_utente_fkey; Type: FK CONSTRAINT; Schema: public; Owner: www
--

ALTER TABLE ONLY public.prenotazioni
    ADD CONSTRAINT prenotazioni_id_utente_fkey FOREIGN KEY (id_utente) REFERENCES public.utenti(id) ON DELETE CASCADE;


-- Completed on 2026-02-20 19:44:35

--
-- PostgreSQL database dump complete
--

\unrestrict pnQANTEHMdIEEFV0YDCu8kjH6s6vy17PFZkaC5tLzeGEkLg942BNAkiuJecdGVv

