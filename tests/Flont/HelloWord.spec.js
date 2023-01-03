import { mount } from '@vue/test-utils'
import HelloWorld from '../../resources/js/Pages/test.vue' //jsconfig.jsonの`@/`がきかない
import { test, expect } from 'vitest'


test('初期表示', () => {
  const wrapper = mount(HelloWorld, {
    props: {
    //   msg: 'hello',
    },
  })

  expect(wrapper.text()).toContain('aaa')
})
