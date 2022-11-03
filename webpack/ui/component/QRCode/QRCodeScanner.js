import CSS from './css/style.module.scss';
import Env from 'bootloader';
import {BrowserCodeReader, BrowserQRCodeReader} from '@zxing/browser';
import 'lib/http/ajax_retry';
import litFlashLight from './assets/litFlashlight.inline.svg';

let $ = require('jquery');

jQuery.fn.extend({


    QRCodeScanner: async function ({withAjax= false, autoStart= false,startBtnHtml="", withFlashlight = false},onStartCallback,resultCallBack) {


        const codeReader = new BrowserQRCodeReader();
        let ele = $(this);
        let ajaxUrl = '';
        if (withAjax)
        {
            ajaxUrl = $(this).attr('data-qrcode-url');

        }

        let _startBtnHtml = '';
        if (!autoStart)
        {
            _startBtnHtml = `<div class="${CSS.start}">${startBtnHtml}</div>`;
        }

        let _flashLightHtml = '';

        if (withFlashlight)
        {
            _flashLightHtml = `<div class="${CSS.flashLight}">${litFlashLight}</div>`;
        }
        let width = $(this).width();

        let scannerHtml = `<div class="${CSS.videoContainer}"><video style="height: ${width}px; width: ${width}px"></video><div class="${CSS.videoOverlay}"></div></div>${_startBtnHtml}`;

        $(this).html(
            scannerHtml
        );
        let videoEle = $(this).find(`video`).first();
        let startBtn = $(this).find(`.${CSS.start}`);

        let controls;


        let currentStream;




        let startScanning = () => {
            let isDecoding = false;

            let videoElement = videoEle[0];

            const videoInputDevices = navigator.mediaDevices.getUserMedia({

                audio: false,
                video: {
                    facingMode: 'environment'
                }
            }).then(function(stream) {
                //video.src = window.URL.createObjectURL(stream);
                videoElement.srcObject = stream;
                videoElement.play();
                currentStream = stream;

                codeReader.decodeFromVideoElement( videoElement, (result, err) => {
                    if (!isDecoding)
                    {
                        if (result) {
                            isDecoding = true;

                            if (onStartCallback)
                            {
                                onStartCallback();
                            }

                            let data = result.text;
                            if (withAjax)
                            {

                                $.ajax({
                                    url: ajaxUrl,
                                    method: "POST",
                                    data: data,
                                    dataType: 'text',
                                    xhrFields: { withCredentials: true }
                                }).done(function(response){
                                    resultCallBack(response);
                                    isDecoding = false;
                                });
                            }
                            else
                            {
                                resultCallBack(data);

                            }
                        }



                    }
                }).then((res) => {
                    controls = res;
                });
            });




        }

        if (autoStart)
        {
            startScanning();
        } else {
            startBtn.click(function () {
                $(this).hide();
                startScanning();
            });
        }

        return {
            stop: () => {
                    if (controls)
                    {
                        controls.stop();
                        if (currentStream)
                        {
                            currentStream.getTracks().forEach((track) => {
                                track.stop();
                            });
                        }

                    }
            }};
    }
});