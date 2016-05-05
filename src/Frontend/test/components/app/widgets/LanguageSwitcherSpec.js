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

import LanguageSwitcher from '../../../../components/app/widgets/LanguageSwitcher';
import LocaleStore from '../../../../store/LocaleStore';
import { stub } from 'sinon';
import { expect } from 'chai';
import { Locale } from '../../../../util/http/facade/HttpServices';
import React from 'react';
import { shallow } from 'enzyme';

describe('LanguageSwitcher', () => {
  it('renders the locales received from flux', () => {
    stub(Locale, 'getLocale', () => 'de');
    stub(LocaleStore, 'getAllLocales', () => ({ de: 'Deutsch' }));
    stub(LocaleStore, 'isInitialized', () => true);

    const markup = shallow(<LanguageSwitcher />);
    setTimeout(() => {
      expect(markup.find('LoadingDropDown')).to.have.length(0);

      const item = markup.find('DropDownItem');
      expect(item.prop('isActive')).to.equal(true);
      expect(item.prop('displayName')).to.equal('Deutsch');
    });

    Locale.getLocale.restore();
    LocaleStore.getAllLocales.restore();
    LocaleStore.isInitialized.restore();
  });

  it('shows loading bar', () => {
    const markup = shallow(<LanguageSwitcher />);
    expect(markup.find('LoadingDropDown')).to.have.length(1);
  });
});
