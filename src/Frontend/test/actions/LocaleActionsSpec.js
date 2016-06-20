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

import { stub, assert, createStubInstance } from 'sinon';
import { changeLocale, loadLanguages } from '../../actions/LocaleActions';
import { expect } from 'chai';
import Locale from '../../util/http/LocaleService';
import LocaleWebAPIUtils from '../../util/api/LocaleWebAPIUtils';
import { runAction } from 'sententiaregum-flux-container';
import UserStore from '../../store/UserStore';
import CurrentLocaleStore from '../../store/CurrentLocaleStore';

describe('LocaleActions', () => {
  it('changes the locale', () => {
    let apiKey = Math.random();

    stub(UserStore, 'getState', () => ({
      is_logged_in: true,
      api_key:      apiKey
    }));
    stub(Locale, 'setLocale', (locale) => expect(locale).to.equal('en'));

    stub(LocaleWebAPIUtils, 'changeUserLocale', (locale) => {
      expect(locale).to.equal('en');
    });

    runAction(changeLocale, ['en']);

    expect(CurrentLocaleStore.getState().locale).to.equal('en');
    assert.calledOnce(LocaleWebAPIUtils.changeUserLocale);
    Locale.setLocale.restore();
    LocaleWebAPIUtils.changeUserLocale.restore();
    UserStore.getState.restore();
  });

  it('loads available locales', () => {
    let response = { de: 'Deutsch', en: 'English' };

    stub(LocaleWebAPIUtils, 'getLocales', (callable) => {
      callable.apply(this, [response]);
    });

    runAction(loadLanguages, []);

    LocaleWebAPIUtils.getLocales.restore();
  });
});
