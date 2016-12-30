/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import { CHANGE_LOCALE } from '../../constants/Locale';
import invariant         from 'invariant';

const initial = {
  available: {
    'de': 'Deutsch',
    'en': 'English'
  },
  currentLocale: 'en'
};

const localeReducer = (state = initial, action) => {
  if (action.type === CHANGE_LOCALE && action.locale !== state.currentLocale) {
    invariant(
      -1 !== Object.keys(initial.available).indexOf(action.locale),
      `Tried to add unsupported locale '${action.locale}' to application's state!`
    );

    return Object.assign({}, initial, {
      currentLocale: action.locale
    });
  }

  return initial;
};

export default localeReducer;
