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
import CurrentLocaleStore from '../../store/CurrentLocaleStore';
import { stub } from 'sinon';
import counterpart from 'counterpart';
import Locale from '../../util/http/LocaleService';

describe('CurrentLocaleStore', () => {
  it('handles locale change', () => {
    runAction(() => {
      return dispatch => dispatch('CHANGE_LOCALE', { locale: 'en' });
    }, []);


    const state = CurrentLocaleStore.getState();

    expect(state.locale).to.equal('en');
  });

  it('handles login', () => {
    stub(Locale, 'getLocale', () => 'de');
    stub(counterpart, 'getLocale', () => 'en');
    stub(counterpart, 'setLocale');

    runAction(() => {
      return dispatch => dispatch('REQUEST_API_KEY', {})
    }, []);

    expect(CurrentLocaleStore.getState().locale).to.equal('de');

    Locale.getLocale.restore();
    counterpart.setLocale.restore();
    counterpart.getLocale.restore();
  });
});
