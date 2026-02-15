CREATE TABLE IF NOT EXISTS public.prenotazioni (
    id SERIAL PRIMARY KEY,
    id_utente INTEGER REFERENCES utenti(id) ON DELETE CASCADE,
    nome_pacchetto text NOT NULL,
    data_prenotazione DATE,
    prezzo NUMERIC(10, 2) NOT NULL
);

ALTER TABLE prenotazioni OWNER TO www;