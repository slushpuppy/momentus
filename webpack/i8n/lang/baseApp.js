class baseApp {
    constructor() {
        this.app = {
            "label": {
                "invalidLoginSession": null
            }
        }
        this.ui = {
            "component": {
                    "errorDialog": {
                        "button": {
                            "close": null,
                            "reportError": null,
                            "contactUs": null,
                            "refresh": null
                        }
                    }
                }
        }

        this.login = {
            "button": {
                "signIn": null
            },
            "input": {
                "emailPlaceholder": null,
                "pincodePlaceholder": null
            },
            "label": {
                "signInDescription": null,
                "signInQrCodeDescription": null,
                "scanfindScanCode": null,
                "scanAuthenticatingDescription": null,
                "scanAuthenticatingSuccess": null,
                "scanAuthenticatingFail": null,
                "pinNewDescription": null,
                "pinValidateDescription": null
            }
        };
        this.dashboard = {


        };

        this.profileHeaderPane = {
            "label": {
                "taskCount": (taskCountNum) => {return null},
                "displayName": (displayName) => {return null}
            }
        }
        this.network = {
            "error": {
                "invalidAppOrUserId": null,
                "invalidQRCode": null,
                "invalidEmail": null,
                "invalidPin": null,
                "invalidPinCreationStrength": null,
                "invalidQRCodeSession": null,
                "invalidUserSession": null,
                "serverError": null,
                "connectivityError": null
            },
            "dashboard" : {
                "feedCardLabel" : {
                    "module": {
                        "fms": {
                            "scheduler": null,
                            "job": null,

                        },
                        "staff": {
                            "overview": null
                        }
                    },
                    "admin": null
                }
            }

        }
    }
}

module.exports = baseApp;