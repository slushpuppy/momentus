import {TabSectionForm} from 'assets/webpack/src/module/form/TabSectionForm/TabSectionForm';
import 'assets/webpack/src/ui/component/bottomSheet/bottomSheet';
let $ = require('jquery');

let form = new TabSectionForm($('#formBuilder'));
let uuid = form.addTab('Personal Info');
let uuid2 = form.addTab('Work Location');
let tab = form.addSection(uuid,'new header');
$('#bottomSheet').bottomSheet();
