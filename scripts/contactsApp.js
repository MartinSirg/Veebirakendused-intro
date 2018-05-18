(function () {
    /**
     * Siin asuvad nimekirja ja lisamislehe nö templated, mida vahetatakse kui vahetada vaadet
     * @type {string}
     */

    let formString = '<form id="addContactForm">\n' +
        '    <table id="table">\n' +
        '        <tr>\n' +
        '            <td>Eesnimi: </td>\n' +
        '            <td><input  name="firstName" id="firstNameField"></td>\n' +
        '        </tr>\n' +
        '        <tr>\n' +
        '            <td>Perekonnanimi: </td>\n' +
        '            <td><input type="text" name="lastName" id="lastNameField"></td>\n' +
        '        </tr>\n' +
        '        <tr>\n' +
        '            <td>Telefon: </td>\n' +
        '            <td><input type="text" name="phone1" id="phone1Field"></td>\n' +
        '        </tr>\n' +
        '        <tr>\n' +
        '            <td>Telefon2: </td>\n' +
        '            <td><input type="text" name="phone2" id="phone2Field"></td>\n' +
        '        </tr>\n' +
        '        <tr>\n' +
        '            <td>Telefon3: </td>\n' +
        '            <td><input type="text" name="phone3" id="phone3Field"></td>\n' +
        '        </tr>\n' +
        '    </table>\n' +
        '    <input  id="btn" name="submit-button" value="Salvesta" type="submit">\n' +
        '</form>';

    let listString = '<table id="contacts">\n' +
        '            <thead>\n' +
        '            <tr>\n' +
        '                <th>Eesnimi</th>\n' +
        '                <th>Perekonnanimi</th>\n' +
        '                <th>Telefonid</th>\n' +
        '            </tr>\n' +
        '            </thead>\n' +
        '            <tbody>\n' +
        '            </tbody>\n' +
        '        </table><br>';
    //------------------------------------------------------------------------------------------------------------------
    //Käivitab alles siis, kui dokument laetud.
    document.addEventListener('DOMContentLoaded', () => {
        fetchContacts();
        document.getElementById("add-page-link").addEventListener("click", (event) => {
            event.preventDefault();
            displayAddContacts();
        });
        document.getElementById("list-page-link").addEventListener("click", (event) => {
            event.preventDefault();
            displayContacts();
        })
    });

    /**
     * Converts html string to a template node
     * https://stackoverflow.com/questions/494143/creating-a-new-dom-element-from-an-html-string-using-built-in-dom-methods-or-pro/35385518#35385518
     */
    function htmlToElement(html) {
        let template = document.createElement('template');
        html = html.trim(); // Never return a text node of whitespace as the result
        template.innerHTML = html;
        return template.content.firstChild;
    }

    /**
     * Muudab vaate kontakti lisamise vaatele
     */
    function displayAddContacts() {
        let parent = document.getElementById("content");

        let block = document.getElementById("success-block");
        if (block != null) parent.removeChild(block);

        let child = parent.children[0];
        let replacement = htmlToElement(formString);
        parent.replaceChild(replacement, child);
        document.getElementById("addContactForm").addEventListener("submit", (event) => {
            event.preventDefault();
            let firstName = event.target.firstName.value;
            let lastName = event.target.lastName.value;
            let phones = [];
            if (event.target.phone1.value !== "") phones.push(event.target.phone1.value);
            if (event.target.phone2.value !== "") phones.push(event.target.phone2.value);
            if (event.target.phone3.value !== "") phones.push(event.target.phone3.value);
            addContact(firstName, lastName, phones);
        })
    }

    /**
     * Muudab vaate kontakti muutmise peale
     * @param id - kontakti id, mille järgi teda muudetakse.
     */
    function displayEditContact(id) {
        let parent = document.getElementById("content");

        let block = document.getElementById("success-block");
        if (block != null) parent.removeChild(block);

        let child = parent.children[0];
        let replacement = htmlToElement(formString);
        parent.replaceChild(replacement, child);
        document.getElementById("addContactForm").addEventListener("submit", (event) => {
            event.preventDefault();
            let firstName = event.target.firstName.value;
            let lastName = event.target.lastName.value;
            let phones = [];
            if (event.target.phone1.value !== "") phones.push(event.target.phone1.value);
            if (event.target.phone2.value !== "") phones.push(event.target.phone2.value);
            if (event.target.phone3.value !== "") phones.push(event.target.phone3.value);
            editContact(firstName, lastName, phones, id)
        });
        fetchContact(id);
    }

    /**
     * Muudab vaate kontakti nimekirja vaate peale
     */
    function displayContacts() {
        let parent = document.getElementById("content");

        let block = document.getElementById("success-block");
        if (block != null) parent.removeChild(block);

        let child = parent.children[0];
        let replacement = htmlToElement(listString);
        parent.replaceChild(replacement, child);
        fetchContacts();
    }

    /**
     * Küsib APIlt JSON kujul infot kontakti kohta
     * @param id - kontakti id
     */
    function fetchContact(id) {
        fetch("?cmd=editContact&contactId=" + id)
            .then(response => response.json())
            .then(fillEditForm)
    }

    /**
     * Küsib APIlt kõik kontaktid
     */
    function fetchContacts() {
        fetch("?cmd=listPage")
            .then(response => response.json())
            .then(addContactsToTable);
    }

    /**
     * Täidab väljad kontakti muutmise vaates.
     * @param contact - kontakt
     */
    function fillEditForm(contact) {
        let error = contact.error;
        if (error == null) {
            document.getElementById("firstNameField").value = contact.firstName;
            document.getElementById("lastNameField").value = contact.lastName;
            let phones = contact.phones;
            if (phones.length >= 1) document.getElementById("phone1Field").value = phones[0];
            if (phones.length >= 2) document.getElementById("phone2Field").value = phones[1];
            if (phones.length >= 3) document.getElementById("phone3Field").value = phones[2];
        } else {
            console.log("ERROR FOUND");
        }
    }

    /**
     * Lisab nimekirja vaate tabelisse kõik kontaktid, iga väli lisatakse erineva funktsiooniga
     * @param contacts
     */
    function addContactsToTable(contacts) {
        let tbody = document.getElementById("contacts").getElementsByTagName("tbody")[0];
        for (let contact of contacts) {
            let row = tbody.insertRow();
            addFirstNameCell(row, contact.firstName, contact.id);
            addTextCell(row, contact.lastName);
            addNumbersCell(row, contact.phones);
        }
    }

    /**
     * Tagastab table cell elemendi, kus on sees anchor element, millega on seotud kontakti nimi ja ID.
     * Anchor elemendil on eventListener, millega saab välja kutsuda kontakti muutmise vaate
     * @param row
     * @param name
     * @param id
     */
    function addFirstNameCell(row, name, id) {
        let cell = document.createElement("td");
        let a = document.createElement("a");
        a.setAttribute("class", "firstName");
        a.appendChild(document.createTextNode(name));
        a.addEventListener("click", (ev) => {
            displayEditContact(id);
        });
        cell.appendChild(a);
        row.appendChild(cell);
    }

    /**
     * Tagastab standartse table cell elemendi tekstiga
     * @param row
     * @param text
     */
    function addTextCell(row, text) {
        let cell = document.createElement("td");
        cell.appendChild(document.createTextNode(text));
        row.appendChild(cell);
    }

    /**
     * Tagastab table cell elemendi, kuhu on lisatud kõik phone elemendid paragraph elementidena
     * @param row
     * @param phones
     */
    function addNumbersCell(row, phones) {
        let cell = document.createElement("td");
        for (let number of phones) {
            let p = document.createElement("p");
            p.appendChild(document.createTextNode(number));
            cell.appendChild(p);
        }
        row.appendChild(cell);
    }

    /**
     * POST request kontakti salvestamiseks, kus edastatakse JSON.
     * @param firstName
     * @param lastName
     * @param phones
     */
    function addContact(firstName, lastName, phones) {
        let payLoad = {
            firstName: firstName,
            lastName: lastName,
            phones: phones
        };

        let options = {
            method: 'POST',
            header: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payLoad)
        };
        fetch("?cmd=addPage", options).then(response => response.json()).then(postAddingController)
    }

    /**
     * POST request kontakti muutmiseks, kus edastatakse JSON.
     * @param firstName
     * @param lastName
     * @param phones
     * @param id
     */
    function editContact(firstName, lastName, phones, id) {
        let payLoad = {
            firstName: firstName,
            lastName: lastName,
            phones: phones
        };
        let options = {
            method: 'POST',
            header: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payLoad)
        };
        let url = "?cmd=editContact&contactId=" + id;
        fetch(url, options).then(response => response.json()).then(postAddingController)
    }

    /**
     * POST requesti järgne funktsioon, kus otsustatakse, kas tuleb näidata veateadet või vahetada nimekirja vaatesse
     * @param response - POST requestist tagastatud JSON
     */
    function postAddingController(response) {
        if (response.hasOwnProperty("errors")) {
           if (document.getElementById("error-block") != null) {
               let child = document.getElementById("error-block");
               let parent = child.parentNode;
               parent.removeChild(child);
           }
           let parent = document.getElementById("addContactForm");
           let errors = document.createElement("div");
           for (let error of response.errors) {
               let p = document.createElement("p");
               p.appendChild(document.createTextNode(error));
               errors.appendChild(p);
           }
           errors.setAttribute("id", "error-block");
           parent.insertBefore(errors, document.getElementById("table"));
        } else {
            displayContacts();
            let parent = document.getElementById("content");
            let success = document.createElement("div");
            let p = document.createElement("p");
            p.appendChild(document.createTextNode("Kontakt lisatud nimekirja"));
            success.appendChild(p);
            success.setAttribute("id", "success-block");
            console.log(success);
            parent.insertBefore(success, document.getElementById("contacts"));
       }
    }
})();
