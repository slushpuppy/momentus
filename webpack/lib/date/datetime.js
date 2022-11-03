export class Datetime {
    static getLocalDateTime(unixTimeSecs)
    {
        let date = new Date(unixTimeSecs*1000);
        let months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        let year = (date.getFullYear() + '').substr(2);
        let month = months[date.getMonth()];
        let day = date.getDate();
        let hours = date.getHours();
        let minutes = date.getMinutes();
        let ampm = hours >= 12 ? 'pm' : 'am';
        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'
        minutes = minutes < 10 ? '0'+minutes : minutes;
        var strTime = hours + ':' + minutes + ' ' + ampm;
        return `${day}${Datetime._nth(day)} ${month} ${year} ${hours}:${minutes} ${ampm}`;
    }
    static _nth(n){return n>3&&n<21?"th":n%10==2?"nd":n%10==2?"nd":n%10==3?"rd":"th"}
}