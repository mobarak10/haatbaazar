export const dateFormat = (date) => {
    return new Date(date).toLocaleDateString('en-UK', {
        year: "numeric",
        month: "long",
        day: "numeric"
    })
}

export const monthFormat = (date) => {
    return new Date(date).toLocaleDateString('en-UK', {
        year: "numeric",
        month: 'long'
    })
}

export const dateFormatWithTime = (date) => {
    return new Date(date).toLocaleString('en-Uk', {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "numeric",
        hour12: true,
        minute: "numeric",
        second: "numeric",
    });
}

export const timeFormat = (date) => {
    return new Date(date).toLocaleString('en-Uk', {
        hour: "numeric",
        hour12: true,
        minute: "numeric",
        second: "numeric",
    });
}


