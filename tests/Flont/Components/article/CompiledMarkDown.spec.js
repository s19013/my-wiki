import { mount } from '@vue/test-utils'
import CompiledMarkDown from '@/Components/article/CompiledMarkDown.vue'
import { it, expect,describe,beforeEach } from 'vitest'

describe('とりあえずマークダウンに変換されているか', () => {

    let wrapper;

    beforeEach(() => {
        wrapper = mount(CompiledMarkDown, {
            props: {
                originalMarkDown: '# hello \n ## hello \n \n ### hello',
            },
        })
        console.log(wrapper.html());
    })

    it("h1タグがあるか", () => {
        expect(wrapper.html()).toContain('<h1 id="hello">hello</h1>')
    })

    it("h2タグがあるか", () => {
        expect(wrapper.html()).toContain('<h2 id="hello-1">hello</h2>')
    })

    it("h3タグがあるか", () => {
        expect(wrapper.html()).toContain('<h3 id="hello-2">hello</h3>')
    })
  })
