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

import LoadableButtonBar from '../../../../components/app/markup/LoadableButtonBar';
import React from 'react';
import TestUtils from 'react/lib/ReactTestUtils';
import ReactDOM from 'react-dom';
import chai from 'chai';

describe('LoadableButtonBar', () => {
  it('renders button bar with a loading spinner', () => {
    const cmp  = TestUtils.renderIntoDocument(<LoadableButtonBar progress={true} btnLabel="Label" />);
    const node = ReactDOM.findDOMNode(cmp);

    chai.expect(node._childNodes[0]._childNodes[0]._nodeValue).to.equal('Label');
    chai.expect(node._childNodes[0]._attributes.class._nodeValue).to.equal('btn btn-primary spinner-btn');
    chai.expect(node._childNodes[1]._attributes.class._nodeValue).to.equal('custom-spinner');
  });
});
