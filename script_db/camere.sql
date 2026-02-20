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
    'SUITE', 
    'Scopri la nostra Suite esclusiva con vasca idromassaggio, sauna e cucina moderna. Il massimo del relax', 
    150.00, 
    's1.jpeg,s2.jpeg,s3.jpeg,s4.jpeg,s5.jpeg,s6.jpeg,s7.jpeg,s8.jpeg'
),
(
    'DELUXE', 
    'Vivi la tua esperienza Deluxe con vasca privata in camera, zona living per cene romantiche e cromoterapia.', 
    100.00, 
    's9.jpeg,s10.jpeg,s11.jpeg,s12.jpeg,s13.jpeg,s14.jpeg,s15.jpeg,s16.jpeg'
);