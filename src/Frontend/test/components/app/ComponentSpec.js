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
import ReactDOM from 'react-dom';
import TestUtils from 'react/lib/ReactTestUtils';
import Component from '../../../components/app/Component';
import chai from 'chai';
import Url from '../../../util/http/facade/Url';
import sinon from 'sinon';
import Cookies from 'cookies-js';
import {ApiKey} from '../../../util/http/facade/HttpServices';

describe('Component', () => {
  it('adapts page with menu', () => {
    const result    = TestUtils.renderIntoDocument(<Test />);
    const component = ReactDOM.findDOMNode(result);

    const Menu    = component._childNodes[0];
    const Content = component._childNodes[1];

    chai.expect(Menu._childNodes[0]._childNodes[1]._childNodes[1]._childNodes).to.have.length(1);
    chai.expect(Content._childNodes[0]._nodeValue).to.equal('Hello World!');
  });

  it('redirects on authentication failure', () => {
    sinon.createStubInstance(Cookies);
    sinon.stub(Url, 'redirect');
    sinon.stub(ApiKey, 'isLoggedIn', () => false);

    const cmp = new ProtectedTest();
    const res = cmp.render();
    sinon.assert.calledOnce(Url.redirect);

    chai.expect(res).to.equal(false);

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
