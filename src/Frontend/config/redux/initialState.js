/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

export default {
  menu: [],
  user: {
    security: {
      authenticated: false,
      appProfile:    {}
    },
    registration: {
      success:          false,
      errors:           {},
      name_suggestions: [],
      id:               null
    },
    activation:     { success: false },
    authentication: {
      success: false,
      message: null
    }
  },
  locales: {
    available: {
      "de": "Deutsch",
      "en": "English"
    },
    currentLocale: 'en'
  },
};
