const testFolder = './lang/';
const fs = require('fs');

let langs = [
    'en'
];

langs.forEach(
    (lang) => {
        fs.readdir(testFolder . lang, (err, files) => {
            files.forEach(file => {
                let {obj} = import('./say.js');


                console.log(JSON.stringify(obj));
            });
        });
    }
)

