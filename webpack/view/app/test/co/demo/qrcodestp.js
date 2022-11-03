import * as ZXing from '@zxing/library';
import './css/qrcodestp.scss';

let $ = require('jquery');
const CryptoJS = require("crypto-js");
const key = `cRfUjXn2r5u8x!A%D*G-KaPdSgVkYp3s`;
const codeReader = new ZXing.BrowserQRCodeReader(300);

function decodeContinuously(codeReader, selectedDeviceId) {
    codeReader.decodeFromInputVideoDeviceContinuously(selectedDeviceId, 'asset-scan-vid', (result, err) => {

        if (result) {


            let raw_decrypt = CryptoJS.AES.decrypt(result.text, "cRfUjXn2r5u8x!A%D*G-KaPdSgVkYp3s");
            if (raw_decrypt != '')
            {
                let decrypted = raw_decrypt.toString(CryptoJS.enc.Utf8);
                console.log(decrypted);
                let json = JSON.parse(decrypted);
                console.log(json);

                let checklist = '';
                for(let i = 0;i < json.checklist.length;i++)
                {
                    checklist += `<li>${json.checklist[i]}</li>`;
                }
                $('#asset-scan-res').html(`<p>
<b>Name:</b> ${json.name}
<b>Weight:</b> ${json.name}
<b>Model:</b> ${json.model}
<b>location:</b> ${json.location}
<b>checklist:</b>
<ul>${checklist}</ul>
</p>`);
            }

        }

        if (err) {

            if (err instanceof ZXing.NotFoundException) {
                //console.log('No QR code found.')
            }

            if (err instanceof ZXing.ChecksumException) {
                //console.log('A code was found, but it\'s read value was not valid.')
            }

            if (err instanceof ZXing.FormatException) {
                //console.log('A code was found, but it was in a invalid format.')
            }
        }
    });

}
$('#asset-scan-start').click(function () {
    decodeContinuously(codeReader, undefined);
})
