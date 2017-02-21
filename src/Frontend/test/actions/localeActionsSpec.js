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

import { spy, assert, stub } from 'sinon';
import { changeLocale }      from '../../actions/localeActions';
import { expect }            from 'chai';
import Locale                from '../../util/http/Locale';
import { CHANGE_LOCALE }     from '../../constants/Locale';
import axios                 from 'axios';

describe('localeActions', () => {
  it('changes the locale', () => {
    const apiKey   = Math.random();
    const state    = () => ({ user: { security: { authenticated: true, appProfile: { apiKey } } } });
    const dispatch = spy();

    stub(axios, 'patch');
    stub(Locale, 'setLocale', locale => expect(locale).to.equal('en'));

    changeLocale('en')(dispatch, state);

    assert.calledOnce(axios.patch);
    expect(axios.patch.calledWith('/api/protected/locale.json', { locale: 'en' }, { headers: { 'X-API-KEY': apiKey } })).to.equal(true);
    assert.calledOnce(dispatch);

    expect(dispatch.calledWith({
      type:   CHANGE_LOCALE,
      locale: 'en'
    }));

    Locale.setLocale.restore();
    axios.patch.restore();
  });
});
