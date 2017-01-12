/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian@mbosch.me>
 * (c) Ben Bieler <ben@benbieler.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import localeReducer     from '../../../reducers/locale/localeReducer';
import { expect }        from 'chai';
import { CHANGE_LOCALE } from '../../../constants/Locale';

describe('localeReducer', () => {
  it('mutates current locale', () => {
    const action = {
      type:   CHANGE_LOCALE,
      locale: 'de'
    };

    expect(localeReducer({
      available: {
        'de': 'Deutsch',
        'en': 'English'
      },
      currentLocale: 'en'
    }, action)).to.deep.equal({
      available: {
        'de': 'Deutsch',
        'en': 'English'
      },
      currentLocale: 'de'
    });
  });

  it('validates given locale', () => {
    const action = {
      type:   CHANGE_LOCALE,
      locale: 'fr'
    };

    expect(() => localeReducer({
      available: {
        'de': 'Deutsch',
        'en': 'English'
      },
      currentLocale: 'de'
    }, action)).to.throw('Tried to add unsupported locale \'fr\' to application\'s state!');
  });
});
