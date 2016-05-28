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
import { stub } from 'sinon';
import { expect } from 'chai';
import Locale from '../../../../util/http/LocaleService';
import React from 'react';
import { shallow } from 'enzyme';
import LocaleWebAPIUtils from '../../../../util/api/LocaleWebAPIUtils';

describe('LanguageSwitcher', () => {
  it('renders the locales received from flux', () => {
    stub(Locale, 'getLocale', () => 'de');
    stub(LocaleWebAPIUtils, 'getLocales', (handler) => handler.apply({ de: 'Deutsch', en: 'English' }));

    const markup = shallow(<LanguageSwitcher />);
    setTimeout(() => {
      expect(markup.find('LoadingDropDown')).to.have.length(0);

      const item = markup.find('DropDownItem');
      expect(item.prop('isActive')).to.equal(true);
      expect(item.prop('displayName')).to.equal('Deutsch');
    });

    Locale.getLocale.restore();
    LocaleWebAPIUtils.getLocales.restore();
  });

  it('shows loading bar', () => {
    const markup = shallow(<LanguageSwitcher />);
    expect(markup.find('LoadingDropDown')).to.have.length(1);
  });
});
