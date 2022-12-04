import { EventBus } from 'quasar'
import { boot } from 'quasar/wrappers'

export default boot(({ app }) => {
  const category = new EventBus()

  // for Composition API
  app.provide('category', category)
})
