import { mount } from '@vue/test-utils'
import { describe, it, expect } from 'vitest'
import StatusBadge from './StatusBadge.vue'

describe('StatusBadge', () => {
  it('delivered berwarna hijau', () => {
    const wrapper = mount(StatusBadge, { props: { status: 'delivered' } })
    expect(wrapper.classes()).toContain('bg-green-100')
  })

  it('pending berwarna kuning', () => {
    const wrapper = mount(StatusBadge, { props: { status: 'pending' } })
    expect(wrapper.classes()).toContain('bg-yellow-100')
  })

  it('status tidak dikenal memakai warna abu-abu', () => {
    const wrapper = mount(StatusBadge, { props: { status: 'lainnya' } })
    expect(wrapper.classes()).toContain('bg-gray-100')
  })
})