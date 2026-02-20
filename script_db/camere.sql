CREATE TABLE IF NOT EXISTS public.camere (
    id SERIAL PRIMARY KEY,
    titolo VARCHAR(100) NOT NULL,
    descrizione TEXT,
    prezzo NUMERIC(10, 2),
    galleria TEXT NOT NULL 
);

ALTER TABLE camere OWNER TO www;

INSERT INTO camere (titolo, descrizione, prezzo, galleria) VALUES 
(
    'AREA SUITE', 
    'Scopri la nostra Suite esclusiva con vasca idromassaggio, sauna e cucina moderna. Il massimo del relax', 
    150.00, 
    's1.jpeg,s2.jpeg,s3.jpeg,s4.jpeg'
),
(
    'AREA DELUXE', 
    'Vivi la casa vacanza Deluxe con vasca privata in camera, zona living per cene romantiche e cromoterapia.', 
    100.00, 
    's5.jpeg,s6.jpeg,s7.jpeg,s8.jpeg'
);