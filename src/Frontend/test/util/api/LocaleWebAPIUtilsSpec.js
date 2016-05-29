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

import { spy, assert, stub } from 'sinon';
import { expect } from 'chai';
import moxios from 'moxios';
import LocaleWebAPIUtils from '../../../util/api/LocaleWebAPIUtils';
import axios from 'axios';
import ApiKey from '../../../util/http/ApiKeyService';

describe('LocaleWebAPIUtils', () => {
  it('loads available locales', () => {
    const response = {
      data: {
        de: 'Deutsch',
        en: 'English'
      }
    }, handler = spy();

    moxios.stubRequest('/api/locale.json', {
      status: 200,
      data:   response
    });

    LocaleWebAPIUtils.getLocales(handler);

    moxios.wait(() => {
      assert.calledOnce(handler);
      expect(handler.calledWith(response));
    });
  });

  it('changes user locale', () => {
    const key = Math.random();

    stub(ApiKey, 'getApiKey', () => key);
    stub(axios, 'patch', (url, data, config) => {
      expect(url).to.equal('/api/protected/locale.json');
      expect(data.locale).to.equal('en');
      expect(config.headers['X-API-KEY']).to.equal(key);
    });

    LocaleWebAPIUtils.changeUserLocale('en');

    assert.calledOnce(axios.patch);
    assert.calledOnce(ApiKey.getApiKey);

    ApiKey.getApiKey.restore();
    axios.patch.restore();
  });
});
