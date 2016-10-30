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
import { changeLocale, loadLanguages } from '../../actions/localeActions';
import { expect } from 'chai';
import Locale from '../../util/http/Locale';
import userStore from '../../store/userStore';
import TestUtils from 'sententiaregum-flux-container/lib/testing/TestUtils';
import { GET_LOCALES, CHANGE_LOCALE } from '../../constants/Locale';
import localeActions from '../../actions/localeActions';
import axios from 'axios';
import promise from '../fixtures/promise';
import ApiKey from '../../util/http/ApiKey';

describe('localeActions', () => {
  it('changes the locale', () => {
    let apiKey = Math.random();

    stub(userStore, 'getStateValue', (path, defaultVal) => {
      if ('auth.authenticated' === path) {
        return true;
      }
      return defaultVal;
    });
    stub(ApiKey, 'getApiKey', () => apiKey);

    stub(axios, 'patch');
    stub(Locale, 'setLocale', (locale) => expect(locale).to.equal('en'));

    TestUtils.executeAction(localeActions, CHANGE_LOCALE, ['en'])({ locale: 'en' });

    assert.calledOnce(axios.patch);
    expect(axios.patch.calledWith('/api/protected/locale.json', { locale: 'en' }, { headers: { 'X-API-KEY': apiKey } })).to.equal(true);

    Locale.setLocale.restore();
    axios.patch.restore();
    userStore.getStateValue.restore();
    ApiKey.getApiKey.restore();
  });

  it('loads available locales', () => {
    let response = { de: 'Deutsch', en: 'English' };

    stub(axios, 'get', promise(true, { data: response }));

    TestUtils.executeAction(localeActions, GET_LOCALES, [])({ de: 'Deutsch', en: 'English' });
    expect(axios.get.calledWith('/api/locale.json')).to.equal(true);
    axios.get.restore();
  });
});
