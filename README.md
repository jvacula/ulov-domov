# UlovDomov - test ukol

## Pokyny
- Výsledný kód umístit na public github repositář a poslat Johnymu odkaz v emailu.
- Je-li cokoliv v zadání nejasné, udělat vlastní rozhodnutí. Lze poslat i dotaz emailem, ale nemohu garantovat okamžitou reakci.
- Vypracování úkolu by mělo zabrat střední až menší jednotky hodin.
- Výsledek nemusí být plně funkční kód, základ je demonstrace modelu a DB návrhu.

## Zadání
- Úkolem je implementovat pouze jednu modelovou část (db + logika). Žádné formuláře, presentery, šablony, ...
- Implementujte funkcionalitu na nastavovaní dvou práv ("Adresář" a "Vyhledávač") k městům pro jednotlivé uživatele.
- Mějme dvě již existující tabulky: `user_admin` a `village`. Očekáváme v nich nějaké záznamy, každý záznam má svoje unikátní ID.
- V tabulce `village` předpokládáme na začátku pouze Praha, Brno.
- Jeden uživatel může mít nastaveno v každém městě relativně téměř libovolně obě práva, tj. například:
    - uživatel Adam má v Praze obě práva ("Adresář" a "Vyhledávač") a v Brně ani jedno.
    - uživatel Bob má v Brně pouze Adresář a v Praze pouze Vyhledávač.
    - uživatel Cyril mám Adresář v obou městech a Vyhledávač jenom v Brně.
    - uživatel Derek není vůbec v tabulce `user_admin` a tím pádem nemá žádná práva. Tj. pokud je uživatel uveden v tabulce user_admin nemůže nemít nějaká práva, tj. musí mít buď všechna nebo nějak omezená, ale nelze/není nutné, aby šlo nastavit, že uživatel nemá žádné právo.
- Pokud nového uživatele Freda přidám do `user_admin`, má bez jakékoliv další akce (ať už na úrovní aplikace či DB/trigger) automaticky všechna práva na všechna města.
- Pokud do village přidám nové město Ostrava, automaticky bez jakékoliv další akce (ať už na úrovní aplikace či DB/trigger) má každý uživatel, co měl do té chvíle všechna práva na všechny města, také práva na Ostravu. Noapak, uživatel, co měl nějaké omezení libovolného práva (např Adresář) v nějakém městě, tak nesmí získat právo na Ostravu (pro Adresář). Zároveň ale pokud uživatel měl u jednoho práva (třeba Vyhledávač) práva na všechny města, získá právo Vyhledávač na Ostravu.
- Konkrétně tedy po přidání Ostravy nastanou u uživatelů tyto změny:
    - Adam: nic nezíská
    - Bob: nic nezíská
    - Cyril: získá právo Adresář pro Ostravu
    - Derek: nic nezíská
    - Fred: získá právo Adresář i Vyhledávač pro Ostravu
- Předpokládáme, že existuje model nad tabulkou `village` který umí načíst kompletní seznam měst v tabulce (nic jiného není potřeba).
- Výsledný model, který se má implementovat, bude mít dvě public metody: set() a get().
- Metoda set() bude očekávat na vstupu dvě proměné: uživatele a pole které se dá očekávat z formuláře, jež pomocí checkobxu umožní uživateli zvolit libovolnou kombinaci práv. Pokud pomyslný formulář bude kompletně nezaškrtnutý, musí uživatel dostat kompletní neomezená práva. Pokud bude pro libovolné právo např. Adresář celý sloupec měst nezaškrtnutý, získá úživatel taktéž pro dané právo přístup ke všem městům.
- Pro zjednodušení lze pouzit dvojrozměrne pole: 
    - [ addressbook => [ 1 => true, 2 => false ] , search => [ 1 => false, 2 => false ] ], kde 1 a 2 jsou ID Praha a Brno.
- Uvedené pole tedy nastaví uživateli právo Adresář pro Prahu a právo Vyhledávač bude mít pro Prahu i Brno.
- Metoda get() bude očekávat na vstupu uživatele a ve druhém parametru specifikované zda chceme práva pro Adresář nebo Vyhledávač. Nasledně metoda vrátí pole jež bude obsahovat všechna města, kam má uživatel právo.
- Předpokládá se, že nový model/třída bude v konstruktoru vyžadovat závislost na modelu pro města pro svou vnitřní činnost.
