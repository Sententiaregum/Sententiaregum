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

import AppDispatcher from '../../dispatcher/AppDispatcher';
import LocaleStore from '../../store/LocaleStore';
import Locale from '../../constants/Locale';
import sinon from 'sinon';
import chai from 'chai';

describe('LocaleStore', () => {
  it('stores available locales', () => {
    const spy = sinon.spy();
    LocaleStore.addChangeListener(spy, 'Locale');

    const data = {
      de: 'Deutsch',
      en: 'English'
    };

    AppDispatcher.dispatch({
      event:  Locale.GET_LOCALES,
      result: data
    });

    chai.expect(spy.called);
    chai.expect(LocaleStore.getAllLocales(), data);
  });
});
