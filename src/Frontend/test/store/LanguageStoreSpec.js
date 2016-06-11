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

import { expect } from 'chai';
import { runAction } from 'sententiaregum-flux-container';
import LanguageStore from '../../store/LanguageStore';

describe('LanguageStore', () => {
  it('handles locale change', () => {
    runAction(() => {
      return dispatch => dispatch('CHANGE_LOCALE', { locale: 'en' });
    }, []);


    const state = LanguageStore.getState();

    expect(state.locale).to.equal('en');
  });
});
