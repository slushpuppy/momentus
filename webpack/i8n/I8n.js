import 'lib/http/ajax_retry';
let $ = require('jquery');


class I8nClass {

    constructor() {
        this.dict = {};

    }
    _addToDict()
    {

    }
    getDict()
    {

    }


    /**
     * @param {string[]} namespaces
     */
    load(namespaces) {
        let p = new Promise((resolve, reject) => {

            let lang = this._getLang();

            for(let i = 0; i < namespaces.length; i++)
            {
                let namespace = namespaces[i];
                $.ajax({
                    url: `./assets/dist/i8n/lang/${lang}/${namespace}.json`,
                    method: "GET",
                    dataType: 'json'
                }).done((data) => {
                    this.dict[namespace] = data;
                    if (Object.keys(this.dict).length === namespaces.length)
                        resolve();
                }).fail(()=>{
                    reject('Error');

                });
            }

        });
        return p;

    }


    getDate(dateString)
    {

    }

    getNumber(numberStr)
    {

    }

    _getLang()
    {
        return this.getAllLanguages().English;
    }

    /**
     * @param namespace
     * @return {App}
     */
    get(namespace) {
        if (this.dict[namespace] && this.dict[namespace])
        {
            return this.dict[namespace];
        }
        throw new Error("Invalid namespace");
    }


    getAllLanguages()
    {
        return {
            'English':'en',
            'Hindi': 'hi'
        }
    }

    getAllNamespaces()
    {
        return {
            'app':'app',
        }
    }
}
const I8n = new I8nClass();
Object.freeze(I8n);

export default I8n;