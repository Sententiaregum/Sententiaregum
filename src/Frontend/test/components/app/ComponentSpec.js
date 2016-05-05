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

import React from 'react';
import Component from '../../../components/app/Component';
import { expect } from 'chai';
import Url from '../../../util/http/facade/Url';
import { stub } from 'sinon';
import { ApiKey } from '../../../util/http/facade/HttpServices';
import { shallow } from 'enzyme';

describe('Component', () => {
  it('adapts page with menu', () => {
    const result = shallow(<Test />),
        menu     = result.find('Menu'),
        content  = result.find('h1');

    expect(menu.prop('items')).to.have.length(1);
    expect(content.contains('Hello World!')).to.equal(true);
  });

  it('handles insufficient credentials', () => {
    stub(Url, 'redirect');
    stub(ApiKey, 'isLoggedIn', () => false);

    expect(shallow(<ProtectedTest />).contains('h1')).to.equal(false);
    expect(Url.redirect.calledOnce).to.equal(true);

    Url.redirect.restore();
    ApiKey.isLoggedIn.restore();
  });
});

class Test extends Component {
  getMenuData() {
    return [
      {
        url: '/#/',
        label: 'Test'
      }
    ]
  }
  renderPage() {
    return <h1>Hello World!</h1>;
  }
}

class ProtectedTest extends Test {
  authConfig = {
    isLoggedIn: true,
    isAdmin:    true
  };
  constructor() {
    super();
  }
}
