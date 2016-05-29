/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import LocaleStore from '../../store/LocaleStore';
import { GET_LOCALES } from '../../constants/Locale';
import { expect } from 'chai';
import { runAction } from 'sententiaregum-flux-container';

describe('LocaleStore', () => {
  it('stores available locales', () => {
    runAction(() => {
      return dispatch => dispatch(GET_LOCALES, { locales: { de: 'Deutsch', en: 'English' } })
    }, []);

    expect(LocaleStore.getState().de).to.equal('Deutsch');
    expect(LocaleStore.getState().en).to.equal('English');
  });
});
