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
import TestUtils from 'react/lib/ReactTestUtils';
import Component from '../../../components/app/Component';
import chai from 'chai';
import Url from '../../../util/http/facade/Url';
import sinon from 'sinon';

describe('Component', () => {
  it('adapts page with menu', () => {
    const renderer = TestUtils.createRenderer();
    renderer.render(<Test />);

    const component = renderer.getRenderOutput();

    const Menu    = component._store.props.children[0];
    const Content = component._store.props.children[1];

    chai.expect(Menu._store.props.items).to.have.length(1);
    chai.expect(Content._store.props.children).to.equal('Hello World!');
  });

  it('redirects on authentication failure', () => {
    sinon.stub(Url, 'redirect');

    const cmp = new ProtectedTest();
    cmp.render();
    sinon.assert.calledOnce(Url.redirect);
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
