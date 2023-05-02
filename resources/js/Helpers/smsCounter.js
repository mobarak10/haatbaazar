export default function smsCounter(message) {
    let data = [];
    let english = /^[~!@#$%^&*(){},.:/-_=+A-Za-z0-9 ]*$/;
    let total_messages = 1;
    if (english.test(message)) {
        data['type'] = 'english';
        let count = (message.length + 11);
        if (count <= 160) {
            total_messages = 1;
        } else if (count <= 306) {
            total_messages = 2;
        } else if (count <= 459) {
            total_messages = 3;
        } else if (count <= 612) {
            total_messages = 4;
        } else if (count <= 765) {
            total_messages = 5;
        } else if (count <= 918) {
            total_messages = 6;
        } else if (count <= 1071) {
            total_messages = 7;
        } else if (count <= 1080) {
            total_messages = 8;
        } else {
            total_messages = "MMS";
        }
    } else {
        data['type'] = 'bangla';
        let count = (message.length + 11);
        if (count <= 63) {
            total_messages = 1;
        } else if (count <= 126) {
            total_messages = 2;
        } else if (count <= 189) {
            total_messages = 3;
        } else if (count <= 252) {
            total_messages = 4;
        } else if (count <= 315) {
            total_messages = 5;
        } else if (count <= 378) {
            total_messages = 6;
        } else if (count <= 441) {
            total_messages = 7;
        } else if (count <= 504) {
            total_messages = 8;
        } else {
            total_messages = "MMS";
        }
    }
    data['total_messages'] = total_messages;

    return data;
}
