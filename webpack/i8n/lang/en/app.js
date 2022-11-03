const baseApp = require("../baseApp");

class App extends baseApp {
    constructor() {
        super();
        this.app.label.invalidLoginSession = "Invalid Session, please login again";


        this.ui.component.errorDialog.button.close = "Close";
        this.ui.component.errorDialog.button.contactUs = "Contact Us";
        this.ui.component.errorDialog.button.refresh = "Refresh";
        this.ui.component.errorDialog.button.reportError = "Report Error";



        this.login.button.signIn = "Sign In";
        this.login.label.signInDescription = "Login To Your Account";
        this.login.label.scanAuthenticatingDescription = "Authenticating..";
        this.login.label.scanAuthenticatingFail = "Invalid Details";
        this.login.label.scanAuthenticatingSuccess = "Welcome!";
        this.login.label.scanfindScanCode = "Find a code to scan";
        this.login.label.signInQrCodeDescription = "Or sign in with";
        this.login.label.pinNewDescription = "Enter a new pincode to protect your account(8 characters)";
        this.login.label.pinValidateDescription = "Enter your pincode";

        this.login.input.emailPlaceholder = "Enter email";
        this.login.input.pincodePlaceholder = "Enter pin";


        this.profileHeaderPane.label.taskCount = (taskCountNum) => {return `You have ${taskCountNum} Tasks today.`;}
        this.profileHeaderPane.label.displayName = (displayName) => {return `Hello ${displayName}`;}


        this.network.error.invalidAppOrUserId = "Invalid AppParam/User";
        this.network.error.invalidEmail = "Invalid Email";
        this.network.error.invalidPin = "Invalid Pin";
        this.network.error.invalidPinCreationStrength = "Pin not strong enough";
        this.network.error.invalidQRCode = "Invalid QR Code";
        this.network.error.invalidQRCodeSession = "Session timed out. Please scan QRCode again";
        this.network.error.invalidUserSession = "Session timed out. Please login again";
        this.network.error.serverError = "Something went wrong, please try again!";
        this.network.error.connectivityError = "Opps, this content can't be loaded, because you're having connectivity problems";

        this.network.dashboard.feedCardLabel.module.fms.scheduler  = "Scheduler";
        this.network.dashboard.feedCardLabel.module.fms.job = "Job";
        this.network.dashboard.feedCardLabel.module.staff.overview  = "Overview";
        this.network.dashboard.feedCardLabel.admin = "Admin";

    }
}


module.exports = App;