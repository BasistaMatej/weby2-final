import { createI18n } from 'vue-i18n'
import en from './locales/en.json'
import sk from './locales/sk.json'

function localeMessage() {
    const locales = [{EN: en}, {SK: sk}]
    const message = {}
    locales.forEach(lang => {
        const key = Object.keys(lang)
        message[key] = lang[key]
    })
    return message
}

export default createI18n({
    locale: 'SK',
    fallbackLocale: 'SK',
    messages: localeMessage()
})