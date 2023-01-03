import { mount } from '@vue/test-utils'
import HelloWorld from '../../resources/js/Pages/test.vue' //jsconfig.jsonの`@/`がきかない
import { test, expect } from 'vitest'


test('テストのテスト', () => {
  const wrapper = mount(HelloWorld, {
    props: {
    //   msg: 'hello',
    },
  })

  expect(wrapper.html()).toContain('aaa')
})
