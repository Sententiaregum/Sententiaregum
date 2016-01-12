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

import TestUtils from 'react/lib/ReactTestUtils';
import ReactDOM from 'react-dom';
import React from 'react';
import ReactPageComponentDecorator from '../../../components/app/ReactPageComponentDecorator';
import chai from 'chai';

describe('ReactPageComponentDecorator', () => {
  it('renders a full page via configuration', () => {
    const HelloWorldMockComponent = React.createClass({
      render: function () {
        return <span>Text</span>;
      }
    });

    const result = TestUtils.renderIntoDocument((
      <ReactPageComponentDecorator app={<HelloWorldMockComponent />} />
    ));

    const component = ReactDOM.findDOMNode(result);
    const node      = component._childNodes[1]._childNodes[0];

    chai.expect(node._nodeValue).to.equal('Text');
  });

  it('converts auth configuration', () => {
    const instance = new ReactPageComponentDecorator(
      {
        authConfig: {
          'isAdmin':    true,
          'isLoggedIn': true
        }
      }
    );

    instance.componentWillMount();
    chai.expect(instance.authConfig.isLoggedIn).to.equal(true);
    chai.expect(instance.authConfig.isAdmin).to.equal(true);
  });
});
