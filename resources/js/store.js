import { createStore } from 'vuex'

export default createStore({
	state: {
        lang:"en"
	},
	mutations: {
        setLang(state,lang){
            state.lang = lang
        },
    },
    actions: {
    },
    modules: {
    },
    getters:{
	}
})
