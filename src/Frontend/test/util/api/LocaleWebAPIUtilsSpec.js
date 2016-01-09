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

import sinon from 'sinon';
import chai from 'chai';
import LocaleWebAPIUtils from '../../../util/api/LocaleWebAPIUtils';
import axios from 'axios';
import {ApiKey} from '../../../util/http/facade/HttpServices';

describe('LocaleWebAPIUtils', () => {
  it('loads available locales', () => {
    const response = {
      data: {
        de: 'Deutsch',
        en: 'English'
      }
    };

    const handler = sinon.spy();
    const promise = {
      then: function (handler) {
        handler.apply(this, [response]);
      }
    };

    sinon.stub(axios, 'get', (url) => {
      chai.expect(url).to.equal('/api/locale.json');
      return promise;
    });

    LocaleWebAPIUtils.getLocales(handler);

    sinon.assert.calledOnce(handler);
  });

  it('changes user locale', () => {
    const key = Math.random();

    sinon.stub(ApiKey, 'getApiKey', () => key);
    sinon.stub(axios, 'patch', (url, data, config) => {
      chai.expect(url).to.equal('/api/protected/locale.json');
      chai.expect(data.locale).to.equal('en');
      chai.expect(config.headers['X-API-KEY']).to.equal(key);
    });

    LocaleWebAPIUtils.changeUserLocale('en');

    sinon.assert.calledOnce(axios.patch);
    sinon.assert.calledOnce(ApiKey.getApiKey);
  });
});
